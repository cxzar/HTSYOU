<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SubmissionRenderer
		The class for rendering item submissions and its assigned positions.
*/
class SubmissionRenderer extends PositionRenderer {

	protected $_item;
    protected $_form;

	/*
		Function: render
			Render objects using a layout file.

		Parameters:
			$layout - Layout name.
			$args - Arguments to be passed to into the layout scope.

		Returns:
			String
	*/
	public function render($layout, $args = array()) {

        // set form
		$this->_form = isset($args['form']) ? $args['form'] : null;
		$this->_item = $this->_form->getItem();

		return parent::render($layout, $args);

	}

	/*
		Function: checkPosition
			Check if position generates output.

		Parameters:
			$position - Position name.

		Returns:
			Boolean
	*/
	public function checkPosition($position) {
		$data_array = $this->_getConfigPosition($position);
		if (!$this->_form->getSubmission()->isInTrustedMode()) {
			foreach($data_array as $data) {
				if ($element = $this->_form->getItem()->getElement($data['element'])) {
					if ($element->getMetaData('trusted') != 'true') {
						return true;
					}
				}
			}
			return false;
		}
		return (bool) count($data_array);
	}

	/* @deprecated as of version 2.5 beta use checkPosition instead */
	public function checkSubmissionPosition($position) {
		return $this->checkPosition($position);
	}

	/*
		Function: renderPosition
			Render submission position output.

		Parameters:
			$position - Position name.
			$args - Arguments to be passed to into the position scope.

		Returns:
			Void
	*/
	public function renderPosition($position, $args = array()) {

		// init vars
		$elements = array();
		$output   = array();
        $trusted_mode = !$this->app->user->get()->guest && $this->_form->getSubmission()->isInTrustedMode();
		$show_tooltip = $this->_form->getSubmission()->showTooltip();

		// get style
		$style = isset($args['style']) ? $args['style'] : 'default';

		// store layout
		$layout = $this->_layout;

		// render elements
        foreach ($this->_getConfigPosition($position) as $data) {
            if (($element = $this->_form->getItem()->getElement($data['element'])) && $field = $this->_form->getFormField($data['element'])) {

				if (!$element->canAccess()) {
					continue;
				}

                if (!$trusted_mode && $element->getMetaData('trusted') == 'true') {
                    continue;
                }

                // bind field data to elements
                if ($field_data = $field->hasError() ? $field->getTaintedValue() : $field->getValue()) {

					if (!$trusted_mode) {
						$field_data = $this->app->submission->filterData($field_data);
					}

                    $element->bindData($field_data);
                } else {
                    $element->bindData();
                }

                // set params
                $params = array_merge((array) $data, $args);

                // check value
                $elements[] = compact('element', 'params', 'field');
            }
        }

        foreach ($elements as $i => $data) {
            $params   = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements)-1)), compact('trusted_mode', 'show_tooltip'), $data['params']);

			// trigger elements beforedisplay event
			$render = true;
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforesubmissiondisplay', array('render' => &$render, 'element' => $data['element'], 'params' => $params)));

			if ($render) {
				$output[$i] = parent::render("element.$style", array('element' => $data['element'], 'field' => $data['field'], 'params' => $params));

				// trigger elements afterdisplay event
				$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:aftersubmissiondisplay', array('html' => &$output[$i], 'element' => $data['element'], 'params' => $params)));
			}

        }

		// restore layout
		$this->_layout = $layout;

		return implode("\n", $output);
	}

	/* @deprecated as of version 2.5 beta use renderPosition instead */
	public function renderSubmissionPosition($position, $args = array()) {
		return $this->renderPosition($position, $args);
	}

    protected function _getConfigPosition($position) {
		$config	= $this->getConfig('item')->get($this->_item->getApplication()->getGroup().'.'.$this->_item->getType()->id.'.'.$this->_layout);

        return $config && isset($config[$position]) ? $config[$position] : array();
    }

}