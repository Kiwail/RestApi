<?php
require_once __DIR__ . '/../http_request.php';

function resti_invoices_delete(array $config, string $id): array
{
    return resti_request($config, 'DELETE', '/api/invoices/' . rawurlencode($id));
}
