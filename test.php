<?php

$config = require __DIR__ . '/lib/resti/config.php';
require_once __DIR__ . '/lib/resti/load_all.php';

/* Contract payload
$payload = 
[
  "client_id" => "383f378e-b669-4d1f-8fa9-be2fb1317a8a",
  "number"=> "CNT-001",
  "status"=> "active",
  "signed_at"=> "2025-01-05"
];
*/
$payload = 
[
  "client_id"=> "4cf41516-ae3f-47a2-be95-f723f24cbfa8",
  "contract_id"=> "10936417-7e6e-4b49-8be0-77de814a273b",
  "number"=> "INV-2025-0001",
  "issued_on"=> "2025-01-10",
  "due_on"=> "2025-01-20",
  "amount_cents"=> 12990,
  "currency"=> "EUR",
  "status"=> "unpaid",
  "comment"=> "Pakalpojumi par janvāri"
];
/*Invoice payload
$payload = 
[
  "client_id"=> "383f378e-b669-4d1f-8fa9-be2fb1317a8a",
  "contract_id"=> "a1b2c3d4-1111-2222-3333-444455556666",
  "number"=> "INV-2025-0001",
  "issued_on"=> "2025-01-10",
  "due_on"=> "2025-01-20",
  "amount_cents"=> 12990,
  "currency"=> "EUR",
  "status"=> "unpaid",
  "comment"=> "Pakalpojumi par janvāri"
];
*/

$res = resti_invoices_create($config,$payload);
//$res = resti_clients_list( $config);
print_r($res);
