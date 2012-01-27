<?php
/**
* @package   yoo_quantum
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

require_once(dirname(__FILE__)."/warp/warp.php");

$warp = Warp::getInstance();

// add paths
$warp['path']->register(dirname(__FILE__).'/warp/systems/joomla.1.7/helpers','helpers');
$warp['path']->register(dirname(__FILE__).'/warp/systems/joomla.1.7/layouts','layouts');
$warp['path']->register(dirname(__FILE__).'/layouts','layouts');
$warp['path']->register(dirname(__FILE__).'/js', 'js');
$warp['path']->register(dirname(__FILE__).'/css', 'css');

// init system
$warp['system']->init();