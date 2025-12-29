<?php
require_once __DIR__ . '/../http_request.php';

function resti_contracts_update_patch(array $config, string $id, array $payload): array
{
    return resti_request($config, 'PATCH', '/api/contracts/' . rawurlencode($id), [], $payload);
}
