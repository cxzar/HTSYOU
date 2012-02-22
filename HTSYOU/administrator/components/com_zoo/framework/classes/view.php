<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

jimport('joomla.application.component.view');

/*
	Class: AppView
		The View Class.
*/
class AppView extends JView {

	/*
		Function: setLayout
			Override to retun this, to allow method chaining.

		Parameters:
			$layout - Layout name

		Returns:
			YView
	*/
	public function setLayout($layout) {
		parent::setLayout($layout);
		return $this;
	}

	/*
		Function: addTemplatePath
			Override to return this, to allow method chaining.

		Parameters:
			$path - Path

		Returns:
			YView
	*/
	public function addTemplatePath($path) {
		parent::addTemplatePath($path);
		return $this;
	}

	/*
		Function: partial
			Render a partial view template file

		Parameters:
			$name - Partial name
			$args - Array of arguments

		Returns:
			String - The output of the the partial
	*/
	public function partial($name, $args = array()) {

		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', '_'.$name);

		// set template path and add global partials
		$path   = $this->_path['template'];
		$path[] = $this->_basePath.DS.'partials';

		// load the partial
		$__file    = $this->_createFileName('template', array('name' => $file));
		$__partial = JPath::find($path, $__file);

		// render the partial
		if ($__partial != false) {

			// import vars and get content
			extract($args);
			ob_start();
			include($__partial);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		return $this->app->error->raiseError(500, 'Partial Layout "'.$__file.'" not found. ('.$this->app->utility->debugInfo(debug_backtrace()).')');
	}

}

/*
	Class: AppViewException
*/
class AppViewException extends AppException {}