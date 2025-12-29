<?php
require_once __DIR__ . '/../http_request.php';

function resti_clients_create(array $config, array $payload): array
{
    return resti_request($config, 'POST', '/api/clients', [], $payload);
}
