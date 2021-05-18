<?php
include('config.php');

use Krizalys\Onedrive\Onedrive;
$client = Onedrive::client($azure_client_id);

$url = $client->getLogInUrl([
    'files.read',
    'files.read.all',
    'files.readwrite',
    'files.readwrite.all',
    'offline_access',
], $azure_redirect_uri);
