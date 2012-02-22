<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JElementZooAccessLevel extends JElement {

	public $_name = 'ZooAccessLevel';

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		// init vars
		$attr  = '';
		$attr .= $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"';
		$attr .= ((string) $node->attributes('disabled') == 'true') ? ' disabled="disabled"' : '';
		$attr .= $node->attributes('size') ? ' size="'.(int) $node->attributes('size').'"' : '';
		$attr .= ((string) $node->attributes('multiple') == 'true') ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $node->attributes('onchange') ? ' onchange="'.(string) $node->attributes('onchange').'"' : '';

		return $app->html->_('zoo.accesslevel', array(), $control_name.'['.$name.']', $attr, 'value', 'text', $value, $control_name.$name);
	}
}
