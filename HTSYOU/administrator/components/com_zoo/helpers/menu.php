<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: MenuHelper
		The general helper Class for menu
*/
class MenuHelper extends AppHelper {

	protected static $_menus = array();

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppTree', 'classes:tree.php');
		$this->app->loader->register('AppMenu', 'classes:menu.php');
	}

	/*
		Function: getInstance
			Get a menu instance

		Parameters:
			$name - Menu name

		Returns:
			AppMenu
	*/
	public function get($name) {

		if (isset(self::$_menus[$name])) {
			return self::$_menus[$name];
		}

		self::$_menus[$name] = $this->app->object->create('AppMenu', array($name));

		return self::$_menus[$name];
	}

}