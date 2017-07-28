<?php

require '..\Client.php';
require '..\Response.php';

use Winco\Antispam\Spamc\Client;
use Winco\Antispam\Spamc\Response;

$client = new Client;
print_r($client->ping());
