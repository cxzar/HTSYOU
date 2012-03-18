<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: FieldHelper
		Field renderer helper class.
*/
class FieldHelper extends AppHelper  {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('helpers:fields'), 'fields');
	}

	/*
		Function: render
			Render a field like text, select or radio button

		Returns:
			String
	*/
	public function render($type, $name, $value, $node, $args = array()) {

		if (empty($type)) return;

		// set vars
		$args['name']  = $name;
		$args['value'] = $value;
		$args['node']  = $node;

		$__file = $this->app->path->path("fields:$type.php");

		if ($__file != false) {
			// render the field
			extract($args);
			ob_start();
			include($__file);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		return 'Field Layout "'.$type.'" not found. ('.$this->app->utility->debugInfo(debug_backtrace()).')';

	}

	/*
		Function: attributes
			Create html attribute string from array

		Returns:
			String
	*/
	public function attributes($attributes, $ignore = array()) {

		$attribs = array();
		$ignore  = (array) $ignore;

		foreach ($attributes as $name => $value) {
			if (in_array($name, $ignore)) continue;

			$attribs[] = sprintf('%s="%s"', $name, htmlspecialchars($value));
		}

		return implode(' ', $attribs);
	}

}