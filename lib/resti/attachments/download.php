<?php
require_once __DIR__ . '/../http_request.php';

function resti_attachments_download(array $config, string $invoiceId, string $attachmentId): array
{
    return resti_request(
        $config,
        'GET',
        '/api/invoices/' . rawurlencode($invoiceId) . '/attachments/' . rawurlencode($attachmentId)
    );
}
