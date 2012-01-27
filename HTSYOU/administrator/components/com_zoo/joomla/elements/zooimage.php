<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementZooImage extends JElement {

	var	$_name = 'ZooImage';

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// load js
		App::getInstance('zoo')->document->addScript('assets:js/image.js');

		// init vars
		$params = $this->_parent;
		$width 	= $params->getValue($name.'_width');
		$height = $params->getValue($name.'_height');

		// create image select html
		$html[] = '<input class="image-select" type="text" name="'.$control_name.'['.$name.']'.'" value="'.$value.'" />';
		$html[] = '<div class="image-measures">';
		$html[] = JText::_('Width').' <input type="text" name="'.$control_name.'['.$name.'_width]'.'" value="'.$width.'" style="width:30px;" />';
		$html[] = JText::_('Height').' <input type="text" name="'.$control_name.'['.$name.'_height]'.'" value="'.$height.'" style="width:30px;" />';
		$html[] = '</div>';

		return implode("\n", $html);
	}

}