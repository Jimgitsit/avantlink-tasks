<?php

use \Gozer\Core\CoreAPI;
use \Gozer\Core\CoreAPIResponseJSON;
use \Gozer\Core\CoreAPIResponseDefault;

/**
 * Class API
 *
 * Sample API for AvantLink.
 */
class TaskAPI extends CoreAPI {
	
	private $returnResult = false;
	
	/**
	 * SampleAPI constructor.
	 * 
	 * Sets up the responder and calls the parent constructor.
	 *
	 * @param bool $returnResult Pass true to return the result of the method instead of responding with JSON.
	 * @documen nodoc
	 */
	public function __construct($returnResult = false) {
		$this->returnResult = $returnResult;
		try {
			if ($this->returnResult) {
				$this->setResponder(new CoreAPIResponseDefault());
			}
			else {
				$this->setResponder(new CoreAPIResponseJSON());
			}
			parent::__construct();
		}
		catch (Exception $e) {
			return $this->respondError($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n" . $e->getTraceAsString());
		}
	}
	
	/**
	 * Returns API version.
	 *
	 * @documen nodoc
	 */
	public function defaultAction() {
		$response = array(
			'api' => 'My API',
			'version' => API_VERSION
		);
		
		return $this->respond($response);
	}
	
	/**
	 * Adds a task.
	 * 
	 * Sample call:
	 * ```
	 * curl -i -X POST -d '{"title":"New Task"}' 'http://domain.com/api/add-task'
	 * ```
	 * 
	 * Response:
	 * ```
	 * {
	 *   "success":true,
	 *   "msg":"Task added",
	 *   "task":{
	 *     "id":67,
	 *     "title":"New Task",
	 *     "created":"08\/25\/2016"
	 *   }
	 * }
	 * ```
	 * 
	 * @param null $data
	 * @return array
	 */
	public function addTask($data = null) {
		if (empty($data)) {
			$data = $this->getPostDataJSON();
		}
		
		// Validation
		if ($data === null) {
			return $this->respondError("Invalid JSON in POST data.");
		}
		
		if (empty($data['title'])) {
			return $this->respondError("Missing title.");
		}
		
		// Check existing
		$em = $this->getEntityManager();
		$task = $em->getRepository('Task')->findOneByTitle($data['title']);
		if (!empty($task)) {
			return $this->respondError("A task with that title already exists.");
		}
		
		// Create the task
		$task = new Task();
		$task->setTitle($data['title']);
		
		$em->persist($task);
		$em->flush();
		
		return $this->respond(array(
			'success' => true,
			'msg' => 'Task added',
			'task' => $task
		));
	}
	
	/**
	 * Returns a task with the given id.
	 * 
	 * Sample call:
	 * ```
	 * curl -i -X GET 'http://domain.com/api/get-task/2'
	 * ```
	 * 
	 * Response:
	 * ```
	 * {
	 *   "id":67,
	 *   "title":"New Task",
	 *   "created":"08\/25\/2016"
	 * }
	 * ```
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getTask($id) {
		if (empty($id)) {
			return $this->respondError("Missing id.");
		}
		
		$em = $this->getEntityManager();
		$task = $em->getRepository('Task')->findOneById($id);
		
		if (empty($task)) {
			return $this->respondError("Task with id $id not found.");
		}
		
		return $this->respond($task);
	}
	
	/**
	 * Returns all tasks.
	 * 
	 * Sample call:
	 * ```
	 * curl -i -X GET 'http://domain.com/api/get-all-tasks'
	 * ```
	 * 
	 * Response:
	 * ```
	 * [
	 *   {
	 *     "id":55,
	 *     "title":"asbsdgf",
	 *     "created":"08\/25\/2016"
	 *   },
	 *   {
	 *     "id":56,
	 *     "title":"lklkj",
	 *     "created":"08\/25\/2016"
	 *   }...
	 * ]
	 * ```
	 * 
	 * @return mixed
	 */
	public function getAllTasks() {
		$em = $this->getEntityManager();
		// TODO: Order by title
		$tasks = $em->getRepository('Task')->findAll();
		
		return $this->respond($tasks);
	}
	
	/**
	 * Removes a task with the given id.
	 * 
	 * Sample call:
	 * ```
	 * curl -i -X GET 'http://domain.com/api/remove-task/2'
	 * ```
	 * 
	 * Response:
	 * ```
	 * {
	 *   "success":true,
	 *   "msg":"Task with id 2 was removed."
	 * }
	 * ```
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function removeTask($id) {
		if (empty($id)) {
			return $this->respondError("Missing id.");
		}
		
		$em = $this->getEntityManager();
		$task = $em->getRepository('Task')->findOneById($id);
		
		if (empty($task)) {
			return $this->respondError("Task with id $id not found.");
		}
		
		$em->remove($task);
		$em->flush();
		
		return $this->respond(array(
			'success' => true,
			'msg' => "Task with id $id was removed."
		));
	}
}