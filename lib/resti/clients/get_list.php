<?php
require_once __DIR__ . '/../http_request.php';

function resti_clients_list(array $config): array
{
    return resti_request($config, 'GET', '/api/clients');
}
