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
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JFormFieldZooSubmission extends JFormField {

	protected $type = 'ZooSubmission';

	public function getInput() {

		// get app
		$app = App::getInstance('zoo');

		jimport('joomla.html.parameter.element');
		$app->loader->register('JElementZooSubmission', 'joomla:elements/zoosubmission.php');

		$element = $app->object->create('JElementZooSubmission');
		$element->set('_parent', $this->form->getValue('params'));
		return $element->fetchElement($this->fieldname, $this->value, $this->element, "jform[{$this->group}]");
		
	}

}