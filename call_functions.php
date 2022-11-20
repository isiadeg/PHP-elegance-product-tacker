<?php
require_once('./functions.php');

$database = database_connect();

Laptop::set_database($database);

Movement::set_database($database);

Sales::set_database($database);

DailyCount::set_database($database);

Faulty::set_database($database);

Dispute::set_database($database);
