<?php
require_once __DIR__ . '/../http_request.php';

function resti_attachments_list(array $config, string $invoiceId): array
{
    return resti_request($config, 'GET', '/api/invoices/' . rawurlencode($invoiceId) . '/attachments');
}
