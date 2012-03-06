<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// check compatibility
if (version_compare(PHP_VERSION, '5.2.4', '>=')) {

	// load class
	require_once(dirname(__FILE__).'/classes/widgetkit.php');

	// get instance and init system
	$widgetkit = Widgetkit::getInstance();
	$widgetkit['system']->init();

}