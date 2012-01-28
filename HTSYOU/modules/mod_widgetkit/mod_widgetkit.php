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

// render widget
if ($widget_id = (int) $params->get('widget_id', '')) {

	// get widgetkit
	$widgetkit = Widgetkit::getInstance();

	// render output
	$output = $widgetkit['widget']->render($widget_id);
	echo ($output === false) ? "Could not load widget with the id $widget_id." : $output;

}