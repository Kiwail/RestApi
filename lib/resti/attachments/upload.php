<?php
require_once __DIR__ . '/../http_request.php';

function resti_attachments_upload_base64(
    array $config,
    string $invoiceId,
    string $filename,
    string $contentType,
    string $base64
): array {
    $payload = [
        'filename' => $filename,
        'content_type' => $contentType,
        'content_base64' => $base64,
    ];

    return resti_request(
        $config,
        'POST',
        '/api/invoices/' . rawurlencode($invoiceId) . '/attachments',
        [],
        $payload
    );
}
