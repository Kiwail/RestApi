<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TenantRegistry;
use App\Models\ApiKey;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class TenantAdminController extends Controller
{
    public function index()
    {
        // uzņēmumu saraksts no resti_core
        $companies = Company::with('tenantRegistry')->orderBy('created_at','desc')->get();

        return view('admin.index', compact('companies'));
    }

    public function store(Request $request)
    {
        // validējam ievadi no formas
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:64|alpha_dash',
            'db_name'   => 'nullable|string|max:64', // nav obligāti norādīt — izģenerēsim
            'key_name'  => 'nullable|string|max:100',
        ]);

        $slug   = strtolower($data['slug']);
        $dbName = $data['db_name'] ?: 'tenant_'.$slug;
        $apiName= $data['key_name'] ?: 'primary';
        $apiPlainSecret = 'sk_'.$slug.'_'.Str::random(20); // parādīsim vienu reizi
        $apiHash = Hash::make($apiPlainSecret);

        // viss master datubāzes transakcijā
        DB::connection('master')->transaction(function () use ($data, $slug, $dbName, $apiName, $apiHash, &$companyId) {
            // 1) izveidojam company ierakstu
            $company = Company::create([
                'id'     => (string) Str::uuid(),
                'name'   => $data['name'],
                'slug'   => $slug,
                'status' => 'active',
            ]);
            $companyId = $company->id;

            // 2) izveidojam nomnieka fizisko DB un shēmu
            $this->createTenantDatabase($dbName);
            $this->bootstrapTenantSchema($dbName);

            // 3) tenant_registry
            TenantRegistry::create([
                'id'         => (string) Str::uuid(),
                'company_id' => $companyId,
                'db_name'    => $dbName,
            ]);

            // 4) api_key
            ApiKey::create([
                'id'            => (string) Str::uuid(),
                'company_id'    => $companyId,
                'name'          => $apiName,
                'prefix'        => $slug, // vienkārši ērta atzīme
                'hashed_secret' => $apiHash,
            ]);
        });

        // Parādīsim administrācijai noslēpumu un gatavu Basic rindu (vienu reizi!)
        $basic = base64_encode($slug.':'.$apiPlainSecret);

        return redirect()
            ->route('admin.index')
            ->with('created_info', [
                'slug'   => $slug,
                'db'     => $dbName,
                'secret' => $apiPlainSecret,
                'basic'  => $basic,
            ]);
    }

    public function show(string $slug)
    {
        // atradīsim uzņēmumu + DB
        $company = Company::where('slug',$slug)->with('tenantRegistry')->firstOrFail();
        $dbName  = $company->tenantRegistry?->db_name;

        if (!$dbName) {
            abort(500, 'Tenant DB not found in registry');
        }

        // uz laiku pārslēgsimies uz nomnieka DB
        $this->switchTenant($dbName);

        // izvilksim nedaudz datus (bez lapošanas, vienkāršības labad)
        $clients   = Client::query()->orderBy('created_at','desc')->limit(50)->get();
        $contracts = Contract::query()->orderBy('created_at','desc')->limit(50)->get();
        $invoices  = Invoice::query()->orderBy('created_at','desc')->limit(50)->get();

        return view('admin.tenant', compact('company','dbName','clients','contracts','invoices'));
    }

    // --- palīgmetodes ---

    protected function createTenantDatabase(string $dbName): void
    {
        // mysql lietotājam ir vajadzīgas CREATE DATABASE privilēģijas
        DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    protected function bootstrapTenantSchema(string $dbName): void
    {
        // pieslēgsimies tikko izveidotajai DB un izveidosim tabulas no tavas shēmas
        DB::statement("USE `$dbName`");

        DB::statement("
            CREATE TABLE IF NOT EXISTS client (
              id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
              auth_user_id  CHAR(36) NULL,
              name          VARCHAR(255) NOT NULL,
              email         VARCHAR(320) NULL,
              phone         VARCHAR(32) NULL,
              created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              KEY idx_auth_user (auth_user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS contract (
              id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
              client_id     CHAR(36) NOT NULL,
              number        VARCHAR(100) NOT NULL,
              status        ENUM('draft','active','closed') NOT NULL DEFAULT 'active',
              signed_at     DATE NULL,
              created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY uq_client_number (client_id, number),
              KEY idx_status (status),
              CONSTRAINT fk_contract_client FOREIGN KEY (client_id)
                REFERENCES client(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS invoice (
              id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
              client_id     CHAR(36) NOT NULL,
              contract_id   CHAR(36) NULL,
              number        VARCHAR(100) NOT NULL,
              issued_on     DATE NOT NULL,
              due_on        DATE NOT NULL,
              currency      CHAR(3) NOT NULL DEFAULT 'EUR',
              amount_cents  BIGINT UNSIGNED NOT NULL,
              status        ENUM('unpaid','paid','void') NOT NULL DEFAULT 'unpaid',
              created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY uq_invoice_number (number),
              KEY idx_client (client_id),
              KEY idx_contract (contract_id),
              CONSTRAINT fk_invoice_client FOREIGN KEY (client_id)
                REFERENCES client(id) ON DELETE RESTRICT,
              CONSTRAINT fk_invoice_contract FOREIGN KEY (contract_id)
                REFERENCES contract(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS invoice_attachment (
              id           CHAR(36) PRIMARY KEY DEFAULT (UUID()),
              invoice_id   CHAR(36) NOT NULL,
              filename     VARCHAR(255) NOT NULL,
              content_type VARCHAR(100) NOT NULL,
              content      LONGBLOB NOT NULL,
              created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              CONSTRAINT fk_attachment_invoice FOREIGN KEY (invoice_id)
                REFERENCES invoice(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    protected function switchTenant(string $dbName): void
    {
        Config::set('database.connections.mysql.database', $dbName);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }
}
