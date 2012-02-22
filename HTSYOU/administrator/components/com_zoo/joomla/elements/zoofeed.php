<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JElementZooFeed extends JElement {

	var	$_name = 'ZooFeed';

	protected static $_count = 1;

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		// load script
		$app->document->addScript('joomla:elements/zoofeed.js');


		// init vars
		$id      			 = 'feed-'.self::$_count++;
		$params 			 = $this->_parent;
		$feed_title 		 = $params->getValue('feed_title');
		$alternate_feed_link = $params->getValue('alternate_feed_link');

		// create html
		$html[] = '<div class="zoo-feed">';
		$html[] = $app->html->_('select.booleanlist', $control_name.'['.$name.']', null , $value);
		$html[] = '<div class="input">';
		$html[] = '<div class="input">';
		$html[] = '<label class="hasTip" title="'.JText::_('OPTIONAL_FEED_TITLE').'" for="'.$id.'">'.JText::_('Feed title').'</label>';
		$html[] = $app->html->_('control.text', $control_name.'[feed_title]', $feed_title);
		$html[] = '</div>';
		$html[] = '<div class="input">';
		$html[] = '<label class="hasTip" title="'.JText::_('ALTERNATE_FEED_LINK').'" for="'.$id.'">'.JText::_('Alternate feed link').'</label>';
		$html[] = $app->html->_('control.text', $control_name.'[alternate_feed_link]', $alternate_feed_link);
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

}