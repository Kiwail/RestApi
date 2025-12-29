<?php
require_once __DIR__ . '/../http_request.php';

function resti_clients_update_patch(array $config, string $id, array $payload): array
{
    return resti_request($config, 'PATCH', '/api/clients/' . rawurlencode($id), [], $payload);
}
