<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementTypes extends JElement {

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		$app = App::getInstance('zoo');

		// get element from parent parameter form
		$config  	 = $this->_parent->element->config;
		$application = $this->_parent->application;

		// init vars
		$attributes = array();
		$attributes['class'] = $node->attributes('class') ? $node->attributes('class') : 'inputbox';
		$attributes['multiple'] = 'multiple';
		$attributes['size'] = $node->attributes('size') ? $node->attributes('size') : '';

		foreach ($application->getTypes() as $type) {
			$options[] = $app->html->_('select.option', $type->id, JText::_($type->name));
		}

		return $app->html->_('select.genericlist', $options, $control_name.'[selectable_type][]', $attributes, 'value', 'text', $config->get('selectable_type', array()));
	}

}