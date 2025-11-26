<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceAttachment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InvoiceAttachmentController extends Controller
{
    // список метаданных вложений счета
    public function index($invoiceId)
    {
        Invoice::findOrFail($invoiceId); // 404 если нет счета

        return InvoiceAttachment::where('invoice_id', $invoiceId)
            ->get(['id','invoice_id','filename','content_type','created_at']);
    }

    // скачать одно вложение (бинарник)
    public function show($invoiceId, $attachmentId)
    {
        $att = InvoiceAttachment::where('invoice_id', $invoiceId)
            ->where('id', $attachmentId)
            ->firstOrFail();

        return response($att->content, 200, [
            'Content-Type'        => $att->content_type,
            'Content-Disposition' => 'inline; filename="'.$att->filename.'"',
        ]);
    }

    // загрузить новое вложение
    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // принимаем multipart/form-data c file=...
        $data = $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', // 5MB для примера
        ]);

        $file     = $data['file'];
        $binary   = file_get_contents($file->getRealPath());
        $mime     = $file->getClientMimeType();
        $filename = $file->getClientOriginalName();

        $att = InvoiceAttachment::create([
            'id'           => Str::uuid()->toString(),
            'invoice_id'   => $invoice->id,
            'filename'     => $filename,
            'content_type' => $mime,
            'content'      => $binary,
        ]);

        return response()->json([
            'id'           => $att->id,
            'filename'     => $att->filename,
            'content_type' => $att->content_type,
            'created_at'   => $att->created_at,
        ], Response::HTTP_CREATED);
    }

    public function destroy($invoiceId, $attachmentId)
    {
        $att = InvoiceAttachment::where('invoice_id', $invoiceId)
            ->where('id', $attachmentId)
            ->firstOrFail();

        $att->delete();

        return response()->noContent();
    }
}
