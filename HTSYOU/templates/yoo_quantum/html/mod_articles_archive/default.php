<?php
/**
* @package   yoo_quantum
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// include config and layout
$base = dirname(dirname(__FILE__));
include($base.'/config.php');
include($warp['path']->path('layouts:'.preg_replace('/'.preg_quote($base, '/').'/', '', __FILE__, 1)));