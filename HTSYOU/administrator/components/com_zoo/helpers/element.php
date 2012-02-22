<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementHelper
		A class that contains element helper functions
*/
class ElementHelper extends AppHelper{

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('Element', 'elements:element/element.php');
	}

	/*
	   Function: getAll
	      Returns an array of all Elements found for this application.

	   Parameters:
	      $application - the application who's elements to retrieve

	   Returns:
	      Array - applications elements
	*/
	public function getAll($application){

		$elements = array();
		$application->registerElementsPath();

		foreach ($this->app->path->dirs('elements:') as $type) {

			if ($type != 'element' && is_file($this->app->path->path("elements:$type/$type.php"))) {
				if ($element = $this->create($type, $application)) {
					if ($element->getMetaData('hidden') != 'true') {
						$elements[] = $element;
					}
				}
			}
		}

		return $elements;
	}

	/*
		Function: create
			Creates element of $type

		Parameters:
			$type - Type of the element subclass to create
	      	$application - the application

		Returns:
			Object - element object
	*/
	public function create($type, $application = false) {

		// load element class
		$elementClass = 'Element'.$type;
		if (!class_exists($elementClass)) {

			if ($application) {
				$application->registerElementsPath();
			}

			$this->app->loader->register($elementClass, "elements:$type/$type.php");

		}

		if (!class_exists($elementClass)) {
			return false;
		}

		$testClass = new ReflectionClass($elementClass);

		if ($testClass->isAbstract()) {
			return false;
		}

		return new $elementClass($this->app);

	}

	/*
		Function: applySeparators
			Separates the passed element values with a separator

		Parameters:
			$separated_by - Separator
			$values - Element values

		Returns:
			String
	*/
	public function applySeparators($separated_by, $values) {

		if (!is_array($values)) {
			$values = array($values);
		}

		$separator = '';
		$tag = '';
		$enclosing_tag = '';
		if ($separated_by) {
			if (preg_match('/separator=\[(.*)\]/U', $separated_by, $result)) {
				$separator = $result[1];
			}

			if (preg_match('/tag=\[(.*)\]/U', $separated_by, $result)) {
				$tag = $result[1];
			}

			if (preg_match('/enclosing_tag=\[(.*)\]/U', $separated_by, $result)) {
				$enclosing_tag = $result[1];
			}
		}

		if (empty($separator) && empty($tag) && empty($enclosing_tag)) {
			$separator = ', ';
		}

		if (!empty($tag)) {
			foreach ($values as $key => $value) {
				$values[$key] = sprintf($tag, $values[$key]);
			}
		}

		$value = implode($separator, $values);

		if (!empty($enclosing_tag)) {
			$value = sprintf($enclosing_tag, $value);
		}

		return $value;
	}

}