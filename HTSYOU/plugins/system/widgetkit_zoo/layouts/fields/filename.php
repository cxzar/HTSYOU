<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// create select
$path    = dirname(dirname(dirname(__FILE__))).$node->attributes()->path;
$options = array();

if (is_dir($path)) {
	foreach (JFolder::files($path, '^([-_A-Za-z0-9]*)\.php$') as $tmpl) {
		$tmpl = basename($tmpl, '.php');
		$options[] = $this->app->html->_('select.option', $tmpl, ucwords($tmpl));
	}
}

echo $this->app->html->_('select.genericlist', $options, $control_name.'['.$name.']', '', 'value', 'text', $value);