<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JElementZooModule extends JElement {

	var	$_name = 'ZooModule';

	function fetchElement($name, $value, &$node, $control_name)	{

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		$options = array($app->html->_('select.option', '', '- '.JText::_('Select Module').' -'));

		return $app->html->_('zoo.modulelist', $options, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);

	}
}
