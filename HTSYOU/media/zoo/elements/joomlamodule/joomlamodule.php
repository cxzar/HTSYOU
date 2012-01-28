<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementJoomlamodule
       The Joomla module wapper element class
*/
class ElementJoomlamodule extends Element {

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// get modules
		$modules = $this->app->module->load();
		$value   = $this->get('value', $this->config->get('default'));

		if ($value && isset($modules[$value])) {
			if ($modules[$value]->published) {

				$rendered = JModuleHelper::renderModule($modules[$value]);

				if (isset($modules[$value]->params)) {
					$module_params = $this->app->parameter->create($modules[$value]->params);
					if ($moduleclass_sfx = $module_params->get('moduleclass_sfx')) {
						$html[] = '<div class="'.$moduleclass_sfx.'">';
						$html[] = $rendered;
						$html[] = '</div>';

						return implode("\n", $html);
					}
				}

				return $rendered;
			}
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Module').' -'));

		return $this->app->html->_('zoo.modulelist', $options, $this->getControlName('value'), null, 'value', 'text', $this->get('value', $this->config->get('default')));

	}

}