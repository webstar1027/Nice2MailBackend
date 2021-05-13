<?php
include('config.php');
use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler; 
use RapidWeb\GooglePeopleAPI\GooglePeople;
use RapidWeb\GooglePeopleAPI\Contact;

if (isset($_GET['access_token'])) {
	$googleOAuth2Handler = new GoogleOAuth2Handler($google_client_id, $google_client_secret, $_GET['scope'], $_GET['access_token']);
	$people = new GooglePeople($googleOAuth2Handler);
	
	if (isset($_GET['contact'])) {

		$contactInfo = json_decode($_GET['contact']);
		if ($contactInfo->resourceName !== '') {
			$contact = $people->get($contactInfo->resourceName);
			$contact->names[0]->givenName  = $contactInfo->name;
			$contact->names[0]->displayName  = $contactInfo->name;
			$contact->names[0]->unstructuredName  = $contactInfo->name;

			$contact->emailAddresses[0]->value  = $contactInfo->email;
			$contact->phoneNumbers[0]->value  = $contactInfo->phoneNumber;
		} else {
			$contact = new Contact($people);

			$contact->names[0] = new stdClass;
			$contact->names[0]->givenName  = $contactInfo->name;

			$contact->emailAddresses[0] = new stdClass;
			$contact->emailAddresses[0]->value  = $contactInfo->email;


			$contact->phoneNumbers[0] = new stdClass;
			$contact->phoneNumbers[0]->value  = $contactInfo->phoneNumber;
		}

		$contact->save();
		   
	}

	if (isset($_GET['resourceName'])) {
		$contact = $people->get($_GET['resourceName']);
		$contact->delete();
	}

	echo json_encode(['contacts' => getAllContacts($people)]);

}

function getAllContacts($people) {
	$contacts = [];

	foreach($people->all() as $contact) {
		$contacts[] = [
			'email' => $contact->emailAddresses[0]->value,
			'name' => $contact->names[0]->displayName,
			'phoneNumber' => $contact->phoneNumbers[0]->value,
			'resourceName' => $contact->resourceName
		];

	}

	return $contacts;
}
