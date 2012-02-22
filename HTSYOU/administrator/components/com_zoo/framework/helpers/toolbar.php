<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ToolbarHelper
		Toolbar helper class. Wrapper for JToolBarHelper.
*/
class ToolbarHelper extends AppHelper {

	/*
		Function: __call
			Map all functions to JToolBarHelper class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/	
    public function __call($method, $args) {
		return $this->_call(array('JToolBarHelper', $method), $args);
    }
	
}