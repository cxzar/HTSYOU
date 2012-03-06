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

class plgContentWidgetkit_Content extends JPlugin {
	
	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		
		preg_match_all('#\[widgetkit id=(\d+)\]#', $article->text, $matches);

		if (count($matches[1])) {

			// load widgetkit
			require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');	

			// get widgetkit
			$widgetkit = Widgetkit::getInstance();

			// render output
			foreach ($matches[1] as $i => $widget_id) {
				$output = $widgetkit['widget']->render($widget_id);
				$output = ($output === false) ? "Could not load widget with the id $widget_id." : $output;
				$article->text = str_replace($matches[0][$i], $output, $article->text);
			}
		}

		return '';
	}

}