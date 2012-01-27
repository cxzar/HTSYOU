<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementRepeatable class
App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');

/*
   Class: ElementDate
   The date element class
*/
class ElementDate extends ElementRepeatable implements iRepeatSubmittable {

	const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {
		return $this->get('value');
	}

	/*
		Function: render
			Renders the repeatable element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	protected function _render($params = array()) {
		$params = $this->app->data->create($params);
		return $this->app->html->_('date', $this->get('value', ''), $this->app->date->format($params->get('date_format') == 'custom' ? $params->get('custom_format') : $params->get('date_format')));
	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/
	protected function _edit(){
		$value = $this->get('value', '');
		$value = !empty($value) ? $this->app->html->_('date', $value, $this->app->date->format(self::EDIT_DATE_FORMAT), $this->app->date->getOffset()) : '';

		$name = $this->getControlName('value');
		return $this->app->html->_('zoo.calendar', $value, $name, $name, array('class' => 'calendar-element'), true);
	}

	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function _renderSubmission($params = array()) {
		$name = $this->getControlName('value');
		return $this->app->html->_('zoo.calendar', $this->get('value', ''), $name, $name, array('class' => 'calendar-element'), true);
	}

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function _validateSubmission($value, $params) {
        $value = $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
				->addOption('date_format', self::EDIT_DATE_FORMAT)
				->clean($value->get('value'));

		return compact('value');
	}

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {
		parent::bindData($data);
		foreach ($this as $self) {
			$value = $this->get('value', '');
			if (!empty($value) && ($value = strtotime($value)) && ($value = strftime(self::EDIT_DATE_FORMAT, $value))) {
				$tzoffset = $this->app->system->config->getValue('config.offset');
				$date     = $this->app->date->create($value, $tzoffset);
				$value	  = $date->toMySQL();
				$this->set('value', $value);
			}
		}
	}

}