<?php
require_once __DIR__ . '/../http_request.php';

function resti_invoices_list(array $config, array $query = []): array
{
    $qs = $query ? ('?' . http_build_query($query)) : '';
    return resti_request($config, 'GET', '/api/invoices' . $qs);
}
