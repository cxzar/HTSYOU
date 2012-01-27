<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementItemCategory
       The item category element class
*/
class ElementItemCategory extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$categories = $this->_item->getRelatedCategories(true);
		return !empty($categories);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		$params = $this->app->data->create($params);
		$values = array();
		foreach ($this->_item->getRelatedCategories(true) as $category) {
			$values[] = $params->get('linked') ? '<a href="'.JRoute::_($this->app->route->category($category)).'">'.$category->name.'</a>' : $category->name;
		}

		return $this->app->element->applySeparators($params->get('separated_by'), $values);
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return null;
	}

}