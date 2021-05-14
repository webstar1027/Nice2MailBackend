<?php
include('config.php');

if (isset($token)) {
	$client->setAccessToken($token);
	$service = new Google_Service_Tasks($client);
	$tasks = []; $tasklists = [];

	if (isset($_GET['task'])) {
		$task = json_decode($_GET['task']);

		if (isset($task->id)) {
			// Update existing event
			$updateTask = $service->tasks->get($task->listId, $task->id);
			$updateTask->setTitle($task->title);
			$updateTask->setNotes($task->notes);
			$updateTask->setDue($task->due);
			$service->tasks->update($task->listId, $task->id, $updateTask);
		} else {
			$taskListId = $task->listId;

			// Create new list
			// $newTask = new Google_Service_Tasks_TaskList();
			// $newTask->setTitle($task->title);
			// $service->tasklists->insert($newTask);

			// add task to given task list
			$newTask = new Google_Service_Tasks_Task([
				'title' => $task->title,
				'notes' => $task->notes,
				'due' => $task->due
			]);
			$service->tasks->insert($taskListId, $newTask);
		}
	}

	if (isset($_GET['id'])) {
		$service->tasks->delete($_GET['listId'], $_GET['id']);
	}

	echo json_encode(['tasks' => getTotalTasks(), 'lists' => $tasklists]);
}


function dateFormat($dateString) {
	$date = new DateTime($dateString);
	return $date->format("Y-m-d");
}

function getTotalTasks() {
	global $service, $tasks, $tasklists;

	$optParams = [
	    'maxResults' => 100,
	];

	$results = $service->tasklists->listTasklists($optParams);

  	if (count($results->getItems()) == 0) {
	    echo json_encode(['tasks' => []]);
	    die();
	} else {
	    foreach ($results->getItems() as $tasklist) {
	    	$taskListId = $tasklist->getId();

	    	$tasklists[] = [
	    		'value' => $taskListId,
	    		'label' => $tasklist->getTitle()
	    	];

	    	if ($taskListId) {
	    		$result_tasks = $service->tasks->listTasks($taskListId);

	    		if (count($result_tasks) > 0) {
	    			foreach ($result_tasks as $item) {
	    				$_task = [
	    					'listId' => $taskListId,
	    					'id' => $item->id,
	    					'title' => $item->title,
	    					'notes' => $item->notes,
	    					'due' => dateFormat($item->due)
	    				];

	    				$tasks[] = $_task;
	    			}
	    		}
	    	}

	    }
	}

	return $tasks;
}