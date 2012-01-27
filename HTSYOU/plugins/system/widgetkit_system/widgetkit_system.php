<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemWidgetkit_System extends JPlugin {

	public function onAfterDispatch() {

		// load widgetkit
		require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');

	}

}