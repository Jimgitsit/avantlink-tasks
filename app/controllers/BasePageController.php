<?php

use \Gozer\Core\CoreController;

/**
 * Class BasePageController
 *
 * Base class for all page controllers.
 */
class BasePageController extends CoreController {
	
	/**
	 * @var array Common container for all twig variables.
	 */
	protected $twigVars = array();
	
	/**
	 * BasePageController constructor.
	 * 
	 * Initializes Twig and adds custom filters.
	 * Also sets the 'env' twig variable from the site config.
	 */
	public function __construct() {
		parent::__construct();
		
		$this->initTwig();
		
		// Add custom filters
		$this->twig->addFilter(new \Twig_SimpleFilter('ucfirst', 'ucfirst'));
		
		$this->twigVars['env'] = ENV;
	}
}