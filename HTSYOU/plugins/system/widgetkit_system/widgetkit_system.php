<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemWidgetkit_System extends JPlugin {

	public function onAfterDispatch() {

		// load widgetkit
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php')) {
			require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');
		}

	}

}