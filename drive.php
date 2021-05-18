<?php
include('config.php');

if (isset($token)) {
	$client->setAccessToken($token);
	$service = new Google_Service_Drive($client);
	$files = [];

	if (isset($_GET['id']) && isset($_GET['email'])) {
		shareFile($_GET['id'], $_GET['email']);
	}

	if (isset($_FILES['file'])) {
		uploadFile($_FILES['file']);
		//echo json_encode($_FILES['file']);
	}

	if (isset($_GET['deleteId'])) {
		$service->files->delete($_GET['deleteId']);
	}

	echo json_encode(['files' => getTotalFiles()]);
}

function uploadFile($uploadfile) {
	global $service;
	$file = new Google_Service_Drive_DriveFile();
    $file->setName($uploadfile['name']);
    $file->setMimeType($uploadfile['type']);

    $data = file_get_contents($uploadfile['tmp_name']);

    $service->files->create($file, array(
      	'data' => $data,
     	'mimeType' => $uploadfile['type'],
      	'uploadType' => 'multipart'
    ));
}

function shareFile($id, $email) {
	global $service;
	$role = 'writer';

	$userPermission = new Google_Service_Drive_Permission(array(
	  'type' => 'user',
	  'role' => $role,
	  'emailAddress' => $email
	));

	$request = $service->permissions->create(
	  $id, $userPermission, array('fields' => 'id')
	);
}

function getTotalFiles() {
	global $service, $files;

	$optParams = array(
	  'pageSize' => 100,
	  'fields' => 'nextPageToken, files(id, name)'
	);

	$results = $service->files->listFiles($optParams);

  	if (count($results->getFiles()) == 0) {
	    echo json_encode(['events' => []]);
	    die();
	} else {
	    foreach ($results->getFiles() as $file) {
	    	$_file = [
	    		'id' => $file->getId(),
	    		'name' => $file->getName()
	    	];

	    	$files[] = $_file;
	    }
	}

	return $files;
}