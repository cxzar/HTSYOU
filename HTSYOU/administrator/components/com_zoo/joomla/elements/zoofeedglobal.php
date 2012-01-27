<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementZooFeedGlobal extends JElement {

	public $_name = 'ZooFeedGlobal';

	protected static $_count = 1;

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		// load script
		$app->document->addScript('joomla:elements/zoofeedglobal.js');

		// init vars
		$params 			 = $this->_parent;
		$id      			 = 'feed-global-'.self::$_count++;
		$feed_title 		 = $params->getValue('feed_title');
		$alternate_feed_link = $params->getValue('alternate_feed_link');
		$global  			 = $params->getValue($name) === null;

		// create html
		$html[] = '<div class="global feed-global">';
		$html[] = '<input id="'.$id.'" type="checkbox" name="_global"'.($global ? ' checked="checked"' : '').' />';
		$html[] = '<label for="'.$id.'"> '.JText::_('Global').'</label>';
		$html[] = '<div class="input">';

		$html[] = $app->html->_('select.booleanlist', ($global ? $id : $control_name.'['.$name.']'), array('role' => $control_name.'['.$name.']') , $value);
		$html[] = '<div class="feed-input">';
		$html[] = '<div class="feed-input">';
		$html[] = '<label class="hasTip" title="'.JText::_('OPTIONAL_FEED_TITLE').'" for="'.$id.'-feed_title">'.JText::_('Feed title').'</label>';
		$html[] = $app->html->_('control.text', ($global ? $id : $control_name.'[feed_title]'), $feed_title, array('id' => $id.'-feed_title', 'class' => 'feed-input-control', 'role' => $control_name.'[feed_title]'));
		$html[] = '</div>';
		$html[] = '<div class="feed-input">';
		$html[] = '<label class="hasTip" title="'.JText::_('ALTERNATE_FEED_LINK').'" for="'.$id.'-alternate_feed_link">'.JText::_('Alternate feed link').'</label>';
		$html[] = $app->html->_('control.text', ($global ? $id : $control_name.'[alternate_feed_link]'), $alternate_feed_link, array('id' => $id.'-alternate_feed_link', 'class' => 'feed-input-control', 'role' => $control_name.'[alternate_feed_link]'));
		$html[] = '</div>';
		$html[] = '</div>';

		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

}