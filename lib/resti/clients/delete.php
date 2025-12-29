<?php
require_once __DIR__ . '/../http_request.php';

function resti_clients_delete(array $config, string $id): array
{
    return resti_request($config, 'DELETE', '/api/clients/' . rawurlencode($id));
}
