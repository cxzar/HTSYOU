<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JButtonZooHelp extends JButton {

	var $_name = 'ZooHelp';

	function fetchButton($type = 'ZooHelp', $ref = '') {

		$text  = JText::_('Help');
		$class = $this->fetchIconClass('help');

		$html  = "<a href=\"$ref\" target=\"_blank\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$text\">\n";
		$html .= "</span>\n";
 		$html .= "$text\n";
		$html .= "</a>\n";

		return $html;
	}

	function fetchId($name) {
		return $this->_parent->get('name').'-'."help";
	}

}