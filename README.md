````md
# Resti Multi-Tenant REST API (Laravel)

Tīmekļa balstīta platforma ar multi-tenant arhitektūru: centrālās datubāzes (resti_core, resti_auth) + atsevišķa tenant datubāze katram uzņēmumam (tenant_{slug}).  
Sistēma nodrošina uzņēmumu, klientu, līgumu, rēķinu un rēķinu pielikumu pārvaldību, kā arī pieteikumus pievienoties uzņēmumam.

## Galvenās iespējas

- Web daļa (sesijas autorizācija)
  - reģistrācija / pieteikšanās
  - “Kompānijas” saraksts un pieteikumu iesniegšana
  - “Manas kompānijas” (tikai ar statusu `active`)
  - “Mani rēķini” (piem., `unpaid`)
  - admin sadaļa uzņēmumu izveidei un apskatei

- REST API (tenant konteksts)
  - `clients` CRUD
  - `contracts` CRUD
  - `invoices` CRUD
  - `invoice attachments` (list/upload/show/delete)
  - `join requests` (list/approve/reject) uzņēmuma kontekstā

## Prasības

- PHP 8.1+ (ieteicams 8.2)
- Composer
- MySQL / MariaDB
- Apache (piem., XAMPP) vai `php artisan serve`

## Instalācija (lokāli)

1) Klonē projektu un ielādē atkarības:
```bash
composer install
````

2. Izveido `.env`:

```bash
copy .env.example .env
php artisan key:generate
```

3. Pieslēgums DB (`.env`)
   Iestati vismaz:

* `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`

Projekts izmanto vairākus savienojumus (piemērs):

* `master` -> `resti_core`
* `auth`   -> `resti_auth`
* `tenant` -> dinamiski pārslēdzas uz `tenant_{slug}` (middleware `tenant.auth`)

Pārliecinies, ka `config/database.php` ir definēti šie connections un `.env` satur to DB nosaukumus.

4. Izveido datubāzes MySQL pusē:

* `resti_core`
* `resti_auth`
  Tenant datubāzes (`tenant_abss`, u.c.) parasti tiek izveidotas automātiski admin darbībās (izveidojot uzņēmumu), ja tev ir uzrakstīta tenant inicializācija.

5. Palaid migrācijas (ja tev migrācijas ir sadalītas pa DB):

```bash
php artisan migrate --database=auth
php artisan migrate --database=master
```

Ja tenant migrācijas tiek palaistas automātiski uzņēmuma izveidē, tad atsevišķi nav jāpalaiž.
Ja tenant migrācijas ir manuāli, tad:

```bash
php artisan migrate --database=tenant
```

6. Palaid projektu:

* XAMPP: novieto projektu `C:\xampp\htdocs\RestApi_Laravel`, atver:

  * `http://localhost/RestApi_Laravel/public`
* vai:

```bash
php artisan serve
```

## Web maršruti (īsi)

* `/register`, `/login`, `/logout`
* `/home` (pēc autorizācijas)
* `/apply` (uzņēmumu saraksts un pieteikumi)
* `/admin` (tikai admin, ar middleware `session.auth` + `session.admin`)

## API autentifikācija (tenant.auth)

API izmanto `HTTP Basic Authorization` ar formu:

* Username: `company_slug`
* Password: `api_secret` (API Secret)

Header:

```
Authorization: Basic base64(company_slug:api_secret)
```

Piemērs:

```
Authorization: Basic YWJzczpza19hYnNzX29mMDJzREdhSEhYYWFpS1owb1Rx
```

Svarīgi:

* `tenant.auth` middleware:

  * nolasa `Authorization` header
  * atrod uzņēmumu `resti_core`
  * pārslēdz DB uz `tenant_{slug}` konkrētajam pieprasījumam
  * ja nav ok, atgriež 401/403

## API bāzes URL

Ja izmanto XAMPP ar `public/`:

* Base URL: `http://localhost/RestApi_Laravel/public`

Svarīgi: neuztaisi `.../api/api/...`
Ja tavā PHP bibliotēkā `base_url` jau beidzas ar `/api`, tad metodes nedrīkst pievienot vēl vienu `/api`.

## API Endpoints (kopsavilkums)

Clients:

* GET    `/clients`
* POST   `/clients`
* GET    `/clients/{id}`
* PUT    `/clients/{id}`
* PATCH  `/clients/{id}`
* DELETE `/clients/{id}`

Contracts:

* GET    `/contracts`
* POST   `/contracts`
* GET    `/contracts/{id}`
* PUT    `/contracts/{id}`
* PATCH  `/contracts/{id}`
* DELETE `/contracts/{id}`

Invoices:

* GET    `/invoices`
* POST   `/invoices`
* GET    `/invoices/{id}`
* PUT    `/invoices/{id}`
* PATCH  `/invoices/{id}`
* DELETE `/invoices/{id}`

Invoice Attachments:

* GET    `/invoices/{invoiceId}/attachments`
* POST   `/invoices/{invoiceId}/attachments`
* GET    `/invoices/{invoiceId}/attachments/{attachmentId}`
* DELETE `/invoices/{invoiceId}/attachments/{attachmentId}`

Join Requests:

* GET    `/join-requests`
* POST   `/join-requests/{id}/approve`
* POST   `/join-requests/{id}/reject`

## Curl piemēri (Windows CMD)

Clients list:

```bat
curl -X GET "http://localhost/RestApi_Laravel/public/api/clients" ^
  -H "Accept: application/json" ^
  -H "Authorization: Basic BASE64_AUTH_HERE"
```

Create contract:

```bat
curl -X POST "http://localhost/RestApi_Laravel/public/api/contracts" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -H "Authorization: Basic BASE64_AUTH_HERE" ^
  -d "{ \"client_id\": \"UUID\", \"number\": \"CNT-001\", \"status\": \"active\", \"signed_at\": \"2025-01-05\" }"
```

## PHP bibliotēka (lokālais klients)

Projekta bibliotēkas ideja: katram endpointam savs fails + viena kopēja `http_request.php`.

Ieteicamā struktūra:

* `lib/resti/http_request.php`
* `lib/resti/load_all.php`
* `lib/resti/config.php`
* `lib/resti/clients/...`
* `lib/resti/contracts/...`
* `lib/resti/invoices/...`
* `lib/resti/attachments/...`
* `lib/resti/join_requests/...`

### config.php piemērs

```php
<?php
return [
  'base_url' => 'http://localhost/RestApi_Laravel/public/api',
  'auth_basic' => 'Basic BASE64_AUTH_HERE',
  'timeout' => 20,
];
```

### Izsaukuma piemērs

```php
<?php
$config = require __DIR__ . '/lib/resti/config.php';
require_once __DIR__ . '/lib/resti/load_all.php';

$payload = [
  "client_id" => "383f378e-b669-4d1f-8fa9-be2fb1317a8a",
  "number" => "CNT-001",
  "status" => "active",
  "signed_at" => "2025-01-05",
];

$res = resti_contracts_create($config, $payload);
print_r($res);

// Contract payload
$payload = 
[
  "client_id" => "383f378e-b669-4d1f-8fa9-be2fb1317a8a",
  "number"=> "CNT-001",
  "status"=> "active",
  "signed_at"=> "2025-01-05"
];
//Invoice payload
$payload = 
[
  "client_id"=> "383f378e-b669-4d1f-8fa9-be2fb1317a8a",
  "contract_id"=> "a1b2c3d4-1111-2222-3333-444455556666",
  "number"=> "INV-2025-0001",
  "issued_on"=> "2025-01-10",
  "due_on"=> "2025-01-20",
  "amount_cents"=> 12990,
  "currency"=> "EUR",
  "status"=> "unpaid",
  "comment"=> "Pakalpojumi par janvāri"
];
```


Padoms:

* izmanto `require_once` tikai uz `load_all.php`, nevis uz katru failu atsevišķi, citādi var sanākt `Cannot redeclare ...`.

## Kļūdas un atbildes formāts

API atbild:

* `200 OK` veiksmīgi
* `201 Created` izveidots resurss
* `401 Unauthorized` nav/nepareizs Authorization
* `403 Forbidden` aizliegts (piem., uzņēmums nav active)
* `404 Not Found` maršruts neeksistē
* `422 Unprocessable Entity` validācijas kļūdas
* `500 Internal Server Error` servera kļūda

## Autors

Ruslans Krutovs (rk23097)