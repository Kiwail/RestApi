<?php
require_once __DIR__ . '/../http_request.php';

function resti_invoices_update_put(array $config, string $id, array $payload): array
{
    return resti_request($config, 'PUT', '/api/invoices/' . rawurlencode($id), [], $payload);
}
