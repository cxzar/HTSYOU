<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

class JElementZooItemorderGlobal extends JElement {

	var $_name = 'ZooItemorderGlobal';

	function fetchElement($name, $value, &$node, $control_name) {

		require_once(dirname(__FILE__) . '/zooitemorder.php');

		// get app
		$app = App::getInstance('zoo');

		// load scripts
		$app->document->addScript('joomla:elements/zooitemorderglobal.js');

		// init vars
		$id			= 'zoo-itemorder-global';
		$params		= $app->parameterform->convertParams($this->_parent);
		$global		= $params->get($name) === null;

		// add global html
		$html = array();
		$html[] = '<div class="global zoo-itemorder-global">';
			$html[] = '<input id="'.$id.'" type="checkbox" name="_global"'.($global ? ' checked="checked"' : '').' />';
			$html[] = '<label for="'.$id.'"> '.JText::_('Global').'</label>';

			$html[] = JElementZooItemorder::fetchElement($name, $value, $node, $control_name);
		$html[] = '</div>';

		$javascript  = "jQuery('#item-order').ZooItemOrderGlobal();";
		$javascripts[]  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return implode("\n", $html).implode("\n", $javascripts);
	}

}