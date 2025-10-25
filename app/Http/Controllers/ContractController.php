<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        // ?client_id=... фильтр по клиенту (минимальный фильтр)
        $query = Contract::query();

        if ($request->has('client_id')) {
            $query->where('client_id', $request->query('client_id'));
        }

        return $query->get();
    }

    public function show($id)
    {
        return Contract::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|string|exists:client,id',
            'number'    => 'required|string|max:100',
            'status'    => 'nullable|in:draft,active,closed',
            'signed_at' => 'nullable|date',
        ]);

        $data['id'] = Str::uuid()->toString();
        $data['status'] = $data['status'] ?? 'active';

        // (uniq per client_id, number) база сама проверит, ловим 23000 при нарушении
        $contract = Contract::create($data);

        return response()->json($contract, 201);
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $data = $request->validate([
            'number'    => 'sometimes|required|string|max:100',
            'status'    => 'sometimes|required|in:draft,active,closed',
            'signed_at' => 'nullable|date',
        ]);

        $contract->update($data);

        return $contract;
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return response()->noContent();
    }
}
