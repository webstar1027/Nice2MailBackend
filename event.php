<?php
include('config.php');

if (isset($token)) {
	$client->setAccessToken($token);
	$service = new Google_Service_Calendar($client);
	$calendarId = 'primary';
	$events = [];

	if (isset($_GET['event'])) {
		$event = json_decode($_GET['event']);

		if (isset($event->id)) {
			// Update existing event
			$updateEvent = $service->events->get($calendarId, $event->id);
			$updateEvent->setSummary($event->summary);

			$eventStart = new Google_Service_Calendar_EventDateTime();
     		$eventStart->setDateTime(date("c", strtotime($event->start_date)));
			$updateEvent->setStart($eventStart);

			$eventEnd = new Google_Service_Calendar_EventDateTime();
     		$eventEnd->setDateTime(date("c", strtotime($event->end_date)));
			$updateEvent->setEnd($eventEnd);
			$service->events->update($calendarId, $updateEvent->getId(), $updateEvent);
		} else {
			// create new event
			$param = [
				'summary' => $event->summary,
				'start' => [
					'dateTime' => date("c", strtotime($event->start_date))
				],
				'end' => [
					'dateTime' => date("c", strtotime($event->end_date))
				]
			];
			$newEvent = new Google_Service_Calendar_Event($param);
			$service->events->insert($calendarId, $newEvent);
		}
		
	}

	if (isset($_GET['id'])) {
		$service->events->delete($calendarId, $_GET['id']);
	}

	echo json_encode(['events' => getTotalEvents()]);
}


function dateFormat($dateString) {
	$date = new DateTime($dateString);
	return $date->format("Y-m-d H:i:s");
}

function getTotalEvents() {
	global $service, $calendarId, $events;

	$optParams = [
	    'maxResults' => 100,
	    'orderBy' => 'startTime',
	    'singleEvents' => TRUE
	];

	$results = $service->events->listEvents($calendarId, $optParams);

  	if (count($results->getItems()) == 0) {
	    echo json_encode(['events' => []]);
	    die();
	} else {
	    foreach ($results->getItems() as $event) {
		    $start = $event->start->dateTime;
		    if (empty($start)) {
		        $start = $event->start->date;
		    }

		    $end = $event->end->dateTime;

		    if (empty($end)) {
		    	$end = $event->end->date;
		    }

	    	$_event = [
	    		'start_date' => dateFormat($start),
	    		'end_date' => dateFormat($end),
	    		'summary' => $event->getSummary(),
	    		'id' => $event->id
	    	];

	    	$events[] = $_event;
	    }
	}

	return $events;
}