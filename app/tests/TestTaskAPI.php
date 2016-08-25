<?php

use PHPUnit\Framework\TestCase;

class TestTaskAPI extends TestCase {
	
	public function testAPILocal() {
		$api = new TaskAPI(true);
		
		// Add task
		$response = $api->addTask(array(
			'title' => 'Test Task'
		));
		
		$this->assertEquals(true, $response['success']);
		$this->assertInstanceOf(Task::class, $response['task']);
		
		// Get task
		$id = $response['task']->getId();
		$task = $api->getTask($id);
		$this->assertInstanceOf(Task::class, $task);
		
		// Remove task
		$response = $api->removeTask($id);
		$this->assertEquals(true, $response['success']);
	}
	
	public function testAPIRemote() {
		// Add task
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POSTFIELDS, array('title' => 'Test Task'));
		curl_setopt($curl, CURLOPT_URL, 'http://local.avantlink.com/api/add-task');
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);
		$this->assertEquals(true, $response['success']);
		
		// Get task
		$id = $response['task']['id'];
		$task = json_decode(file_get_contents("http://local.avantlink.com/api/get-task/$id"), true);
		$task = new Task();
		// TODO: Test props
		
		// Remove task
		$response = json_decode(file_get_contents("http://local.avantlink.com/api/remove-task/$id"), true);
		$this->assertEquals(true, $response['success']);
	}
}