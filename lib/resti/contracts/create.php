<?php
require_once __DIR__ . '/../http_request.php';

function resti_contracts_create(array $config, array $payload): array
{
    return resti_request($config, 'POST', '/api/contracts', [], $payload);
}
