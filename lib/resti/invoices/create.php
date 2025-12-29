<?php
require_once __DIR__ . '/../http_request.php';

function resti_invoices_create(array $config, array $payload): array
{
    return resti_request($config, 'POST', '/api/invoices', [], $payload);
}
