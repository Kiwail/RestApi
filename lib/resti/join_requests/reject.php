<?php
require_once __DIR__ . '/../http_request.php';

function resti_join_requests_reject(array $config, string $id): array
{
    return resti_request($config, 'POST', '/api/join-requests/' . rawurlencode($id) . '/reject');
}
