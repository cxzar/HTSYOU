<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TemplateHelper
		The general helper Class for template
*/
class TemplateHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppTemplate', 'classes:template.php');
	}

	/*
		Function: create
			Get a template instance

		Returns:
			AppTemplate
	*/
	public function create($args = array()) {
		$args = (array) $args;
		return $this->app->object->create('AppTemplate', $args);
	}

}