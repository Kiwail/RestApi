<?php
// lib/resti/errors.php

function resti_error_result(string $code, string $reason, int $status, $details = null): array
{
    return [
        'ok' => false,
        'status' => $status,
        'data' => null,
        'raw' => '',
        'error' => [
            'code' => $code,
            'reason' => $reason,
            'details' => $details,
        ],
    ];
}

function resti_http_to_code(int $status): string
{
    return match (true) {
        $status === 400 => 'HTTP_400_BAD_REQUEST',
        $status === 401 => 'HTTP_401_UNAUTHORIZED',
        $status === 403 => 'HTTP_403_FORBIDDEN',
        $status === 404 => 'HTTP_404_NOT_FOUND',
        $status === 409 => 'HTTP_409_CONFLICT',
        $status === 422 => 'HTTP_422_VALIDATION',
        $status >= 500  => 'HTTP_5XX_SERVER_ERROR',
        default         => 'HTTP_ERROR',
    };
}
