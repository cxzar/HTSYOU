<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

// load widgetkit
require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');

class JFormFieldWidget extends JFormField {

	protected $type = 'Widget';

	function getInput() {

		// get widgetkit
		$widgetkit = Widgetkit::getInstance();

		return $widgetkit['field']->render('widget', $this->name, $this->value, null);
	}

}