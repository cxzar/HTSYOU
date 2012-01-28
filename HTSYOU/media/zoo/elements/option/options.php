<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementOptions extends JElement {

	function fetchElement($name, $value, $node, $control_name) {

		// get app instance
		$app = App::getInstance('zoo');

		// get element from parent parameter form
		$element = $this->_parent->element;
		$config  = $element->config;

		// init vars
		$id       = str_replace(array('[', ']'), '_', $control_name).'option';
		$i        = 0;

		// create options
		$options  = '<div id="'.$id.'" class="options">';
		$options .= '<ul>';
		foreach ($config->get('option', array()) as $opt) {
			$options .= '<li>'.$element->editOption($control_name, $i++, $opt['name'], $opt['value']).'</li>';
		}
		$options .= '<li class="hidden" >'.$element->editOption($control_name, '0', '', '').'</li>';
		$options .= '</ul>';
		$options .= '<div class="add">'.JText::_('Add Option').'</div>';
		$options .= '</div>';

		// create js
		$javascript  = "jQuery('#$id').ElementSelect({variable: '$control_name' });";
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return $options.$javascript;
	}

}