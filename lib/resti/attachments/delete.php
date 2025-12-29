<?php
require_once __DIR__ . '/../http_request.php';

function resti_attachments_delete(array $config, string $invoiceId, string $attachmentId): array
{
    return resti_request(
        $config,
        'DELETE',
        '/api/invoices/' . rawurlencode($invoiceId) . '/attachments/' . rawurlencode($attachmentId)
    );
}
