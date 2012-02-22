<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: PluginHelper
		The general helper Class for plugins
*/
class PluginHelper extends AppHelper {

	/*
	   Function: enable
	       Enable Joomla plugin.

	   Returns:
	       void
	*/
	public function enable($plugin) {

		if ($this->app->joomla->isVersion('1.5')) {
			$this->app->database->query("UPDATE #__plugins SET published = 1 WHERE element = '$plugin'");
		} else {
			$this->app->database->query("UPDATE #__extensions SET enabled = 1 WHERE element = '$plugin'");
		}

	}

}