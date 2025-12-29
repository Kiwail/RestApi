<?php
require_once __DIR__ . '/../http_request.php';

function resti_contracts_delete(array $config, string $id): array
{
    return resti_request($config, 'DELETE', '/api/contracts/' . rawurlencode($id));
}
