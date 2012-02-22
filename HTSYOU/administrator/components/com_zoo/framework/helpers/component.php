<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ComponentHelper
		Helper to access components configuration.
*/
class ComponentHelper extends AppHelper {

	/* components */
	protected static $_components = array();

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppComponent', 'classes:component.php');
	}

	/*
		Function: __get
			Retrieve a component

		Parameters:
			$name - Component name

		Returns:
			Mixed
	*/	
	public function __get($name) {

		// get component name
		if ($name == 'self') {
			$name = 'com_'.$this->app->id;
		} elseif ($name == 'active') {
			$name = $this->app->system->application->scope;
		} elseif (strpos($name, 'com_') === false) {
			$name = 'com_'.$name;
		}

		// add component, if not exists
		if (!isset(self::$_components[$name])) {
			self::$_components[$name] = new AppComponent($this->app, $name);
		}

		return self::$_components[$name];
	}

}