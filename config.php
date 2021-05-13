<?php

require_once 'vendor/autoload.php';

$client = new Google_Client();
$google_client_id = '290880609641-uiokj73etdah9h0fllkatjf4j8gii0hn.apps.googleusercontent.com';
$google_client_secret = 'lVEeEPJ3pdpCVAFciq1ZZGvH';
$google_redirect_uri = 'http://localhost:8080';
$api_key = 'AIzaSyBCUkzA23H9RF1LAGgRsFxWm-a6fJ2L0TI';


$client->setClientId($google_client_id);
$client->setClientSecret($google_client_secret);
$client->setRedirectUri($google_redirect_uri);
$client->setDeveloperKey($api_key);
$client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
$client->addScope("email");
$client->addScope("profile");
session_start();

// Remove cors error in php server
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}


$headers = getallheaders();

if (isset($headers['Authorization'])) {
	$token = str_replace('Bearer ', '', $headers['Authorization']);
	$client->setAccessToken($token);
}