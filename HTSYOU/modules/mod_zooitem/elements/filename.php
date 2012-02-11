<?php
/**
* @package   ZOO Item
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementFilename extends JElement {

	var	$_name = 'Filename';

	function fetchElement($name, $value, &$node, $control_name) {

		// get app
		$app = App::getInstance('zoo');

		// create select
		$path    = dirname(dirname(__FILE__)).$node->attributes('path');
		$options = array();

		if (is_dir($path)) {
			foreach (JFolder::files($path, '^([-_A-Za-z0-9]+)\.php$') as $tmpl) {
				$tmpl = basename($tmpl, '.php');
				$options[] = $app->html->_('select.option', $tmpl, ucwords($tmpl));
			}
		}

		return $app->html->_('select.genericlist', $options, $control_name.'['.$name.']', '', 'value', 'text', $value);
	}

}