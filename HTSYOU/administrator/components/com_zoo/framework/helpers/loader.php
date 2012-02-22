<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: LoaderHelper
		Class loader helper class. Wrapper for JLoader.
*/
class LoaderHelper extends AppHelper {

	/*
		Function: register
			Register a class with the loader.

		Parameters:
			$class - Class name
			$file - File name

		Returns:
			Void
	*/
	public function register($class, $file) {
		if (!class_exists($class)) {
			JLoader::register($class, $this->app->path->path($file));
		}
	}

	/*
		Function: __call
			Map all functions to JLoader class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/
    public function __call($method, $args) {
		return $this->_call(array('JLoader', $method), $args);
    }

}