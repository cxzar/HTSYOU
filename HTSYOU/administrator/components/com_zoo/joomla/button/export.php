<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JButtonExport extends JButton
{
	var $_name = 'Export';

	function fetchButton( $type='Export', $controller = '')
	{
		global $option;
		$text	= JText::_('Export');
		$class	= $this->fetchIconClass('archive');
		$doTask	= $this->_getCommand();

		$html	= "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$text\">\n";
		$html .= "</span>\n";
		$html	.= "$text\n";
		$html	.= "</a>\n";

		return $html;
	}

	function fetchId($name)
	{
		return $this->_parent->_name.'-'."export";
	}

	function _getCommand()
	{
		//set format to raw, execute submitbutton and then change back to format html (adminform needs to have property format)
		$cmd = "document.adminForm.format.value='raw'; "
				. "if(document.adminForm.boxchecked.value==0){alert('Please make a selection from the list to export');}else{  submitbutton('doexport')}; "
				. "document.adminForm.format.value='html';";

		return $cmd;
	}
}
