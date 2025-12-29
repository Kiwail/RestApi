<?php
require_once __DIR__ . '/errors.php';

function resti_with_company(array $config, string $basic): array
{
    $config['basic'] = $basic;
    return $config;
}

function resti_request(
    array $config,
    string $method,
    string $path,
    array $headers = [],
    $jsonBody = null
): array {
    $base = rtrim($config['base_url'] ?? '', '/');
    $url  = $base . $path;

    $ch = curl_init($url);

    $defaultHeaders = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . ($config['basic'] ?? ''),
    ];

    $allHeaders = array_merge($defaultHeaders, $headers);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HTTPHEADER     => $allHeaders,
        CURLOPT_TIMEOUT        => (int)($config['timeout'] ?? 20),
    ]);

    if ($jsonBody !== null) {
        $payload = is_string($jsonBody) ? $jsonBody : json_encode($jsonBody, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    $raw = curl_exec($ch);

    if ($raw === false) {
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        return resti_error_result(
            'CURL_ERROR',
            "cURL error #{$errno}: {$err}",
            0,
            ['curl_errno' => $errno, 'curl_error' => $err, 'url' => $url]
        );
    }

    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = null;
    $jsonErr = null;

    if ($raw !== '') {
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $jsonErr = json_last_error_msg();
        }
    }

    if ($status >= 300 && $status < 400) {
        return resti_error_result(
            'HTTP_REDIRECT',
            'Server returned redirect (probably web route instead of /api/... or wrong base_url).',
            $status,
            ['hint' => 'Check base_url + path. Must be .../public and /api/...']
        );
    }

    if ($jsonErr !== null && $raw !== '') {
        return resti_error_result(
            'NON_JSON_RESPONSE',
            'Response is not valid JSON (maybe HTML error page).',
            $status,
            ['json_error' => $jsonErr, 'raw_head' => substr($raw, 0, 400)]
        );
    }

    if ($status >= 400) {
        $reason = (is_array($data) && isset($data['message'])) ? (string)$data['message'] : 'HTTP error';
        return resti_error_result(
            resti_http_to_code($status),
            $reason,
            $status,
            ['response' => $data]
        );
    }

    return [
        'ok' => true,
        'status' => $status,
        'data' => $data,
        'raw' => $raw,
        'error' => null,
    ];
}
