<?php
// Подключай один файл — и всё готово
require_once __DIR__ . '/http_request.php';

// clients
require_once __DIR__ . '/clients/get_list.php';
require_once __DIR__ . '/clients/get_one.php';
require_once __DIR__ . '/clients/create.php';
require_once __DIR__ . '/clients/update_put.php';
require_once __DIR__ . '/clients/update_patch.php';
require_once __DIR__ . '/clients/delete.php';

// contracts
require_once __DIR__ . '/contracts/get_list.php';
require_once __DIR__ . '/contracts/get_one.php';
require_once __DIR__ . '/contracts/create.php';
require_once __DIR__ . '/contracts/update_put.php';
require_once __DIR__ . '/contracts/update_patch.php';
require_once __DIR__ . '/contracts/delete.php';

// invoices
require_once __DIR__ . '/invoices/get_list.php';
require_once __DIR__ . '/invoices/get_one.php';
require_once __DIR__ . '/invoices/create.php';
require_once __DIR__ . '/invoices/update_put.php';
require_once __DIR__ . '/invoices/update_patch.php';
require_once __DIR__ . '/invoices/delete.php';

// attachments
require_once __DIR__ . '/attachments/list.php';
require_once __DIR__ . '/attachments/upload.php';
require_once __DIR__ . '/attachments/download.php';
require_once __DIR__ . '/attachments/delete.php';

// join_requests
require_once __DIR__ . '/join_requests/list.php';
require_once __DIR__ . '/join_requests/approve.php';
require_once __DIR__ . '/join_requests/reject.php';
