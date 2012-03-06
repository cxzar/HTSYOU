<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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