<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JElementZooItemorder extends JElement {

	var $_name = 'ZooItemorder';

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		return App::getInstance('zoo')->field->render('zooitemorder', $name, $value, $node, array('control_name' => $control_name, 'parent' => $this->_parent));

	}

}