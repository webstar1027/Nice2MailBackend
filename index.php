<?php
include('config.php');
use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler; 
use RapidWeb\GooglePeopleAPI\GooglePeople;

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


if (isset($_GET['access_token'])) {
	$contacts = [];
	$googleOAuth2Handler = new GoogleOAuth2Handler($google_client_id, $google_client_secret, $_GET['scope'], $_GET['access_token']);
    $people = new GooglePeople($googleOAuth2Handler);
   	
   	foreach($people->all() as $contact) {
        $contacts[] = [
            'email' => $contact->emailAddresses[0]->value,
            'name' => $contact->names[0]->displayName,
            'phoneNumber' => $contact->phoneNumbers[0]->value,
        ];
	}


    echo json_encode(['contacts' => $contacts]);
}

