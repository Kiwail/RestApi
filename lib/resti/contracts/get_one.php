<?php
require_once __DIR__ . '/../http_request.php';

function resti_contracts_get(array $config, string $id): array
{
    return resti_request($config, 'GET', '/api/contracts/' . rawurlencode($id));
}
