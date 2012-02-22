<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JFormFieldZooApplication extends JFormField {

	protected $type = 'ZooApplication';

	public function getInput() {

		// get app
		$app = App::getInstance('zoo');

		jimport('joomla.html.parameter.element');
		$app->loader->register('JElementZooApplication', 'joomla:elements/zooapplication.php');

		$element = $app->object->create('JElementZooApplication');
		$element->set('_parent', $this->form->getValue('params'));
		return $element->fetchElement($this->fieldname, $this->value, $this->element, "jform[{$this->group}]");
		
	}

}