<?php
require_once __DIR__ . '/../http_request.php';

function resti_clients_get(array $config, string $id): array
{
    return resti_request($config, 'GET', '/api/clients/' . rawurlencode($id));
}
