<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementFilename extends JElement {

	var	$_name = 'Filename';

	function fetchElement($name, $value, &$node, $control_name) {

		$app = App::getInstance('zoo');

		// create select
		$path    = dirname(dirname(dirname(__FILE__))).$node->attributes('path');
		$options = array();

		if (is_dir($path)) {
			foreach (JFolder::files($path, '^([-_A-Za-z0-9]*)\.php$') as $tmpl) {
				$tmpl = basename($tmpl, '.php');
				$options[] = JHTML::_('select.option', $tmpl, ucwords($tmpl));
			}
		}

		return $app->html->_('select.genericlist', $options, $control_name.'['.$name.']', '', 'value', 'text', $value);
	}

}