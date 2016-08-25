<?php

/**
 * Class TasksPage
 *
 * Controller for the tasks page.
 */
class TasksPage extends BasePageController {
	
	/**
	 * Gets all tasks for the template engine.
	 */
	public function defaultAction() {
		
		$em = $this->getEntityManager();
		$tasks = $em->getRepository('Task')->findAll();
		$this->twigVars['tasks'] = $tasks;
		
		echo $this->twig->render('tasks.twig', $this->twigVars);
	}
}