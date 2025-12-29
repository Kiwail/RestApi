<?php
require_once __DIR__ . '/../http_request.php';

function resti_invoices_get(array $config, string $id): array
{
    return resti_request($config, 'GET', '/api/invoices/' . rawurlencode($id));
}
