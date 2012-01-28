<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load widgetkit
require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');

class JElementWidget extends JElement {

	var	$_name = 'Widget';

	function fetchElement($name, $value, &$node, $control_name) {

		// get widgetkit
		$widgetkit = Widgetkit::getInstance();

		return $widgetkit['field']->render('widget', $control_name.'['.$name.']', $value, null);
	}

}