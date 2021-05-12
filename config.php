<?php

require_once 'vendor/autoload.php';

$client = new Google_Client();
$google_client_id = '290880609641-uiokj73etdah9h0fllkatjf4j8gii0hn.apps.googleusercontent.com';
$google_client_secret = 'lVEeEPJ3pdpCVAFciq1ZZGvH';
$google_redirect_uri = 'http://localhost:8080';


$client->setClientId($google_client_id);
$client->setClientSecret($google_client_secret);
$client->setRedirectUri($google_redirect_uri);
$client->addScope("email");
$client->addScope("profile");
session_start();