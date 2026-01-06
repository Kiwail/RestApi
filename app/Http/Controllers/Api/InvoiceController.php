<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Mini-filtri: client_id, status
        $q = Invoice::query();

        if ($request->has('client_id')) {
            $q->where('client_id', $request->query('client_id'));
        }

        if ($request->has('status')) {
            $q->where('status', $request->query('status'));
        }

        return $q->get();
    }

    public function show($id)
    {
        // ielādējam attachments (metadati, bez binārā satura)
        return Invoice::with(['attachments:id,invoice_id,filename,content_type,created_at'])
            ->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'    => 'required|string|exists:client,id',
            'contract_id'  => 'nullable|string|exists:contract,id',
            'number'       => 'required|string|max:100',
            'issued_on'    => 'required|date',
            'due_on'       => 'required|date',
            'currency'     => 'nullable|string|size:3',
            'amount_cents' => 'required|integer|min:0',
            'status'       => 'nullable|in:unpaid,paid,void',
        ]);

        $data['id'] = Str::uuid()->toString();
        $data['currency'] = $data['currency'] ?? 'EUR';
        $data['status']   = $data['status']   ?? 'unpaid';

        $invoice = Invoice::create($data);

        return response()->json($invoice, 201);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $data = $request->validate([
            'contract_id'  => 'nullable|string|exists:contract,id',
            'number'       => 'sometimes|required|string|max:100',
            'issued_on'    => 'sometimes|required|date',
            'due_on'       => 'sometimes|required|date',
            'currency'     => 'sometimes|required|string|size:3',
            'amount_cents' => 'sometimes|required|integer|min:0',
            'status'       => 'sometimes|required|in:unpaid,paid,void',
        ]);

        $invoice->update($data);

        return $invoice;
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->noContent();
    }
}
