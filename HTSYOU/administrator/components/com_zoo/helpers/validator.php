<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ValidatorHelper
		The general helper Class for validators
*/
class ValidatorHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppValidator', 'classes:validator.php');
	}

	/*
		Function: create
			Creates a validator instance

		Parameters:
			$type - Validator type

		Returns:
			AppValidator
	*/
	public function create($type = '') {

		$args = func_get_args();
		$type = array_shift($args);

		$class = 'AppValidator' . $type;

		// load class
		$this->app->loader->register($class, 'classes:validator.php');
		return $this->app->object->create($class, $args);

	}

}