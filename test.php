<?php

$config = require __DIR__ . '/lib/resti/config.php';
require_once __DIR__ . '/lib/resti/load_all.php';


$res = resti_clients_list($config);
print_r($res);
