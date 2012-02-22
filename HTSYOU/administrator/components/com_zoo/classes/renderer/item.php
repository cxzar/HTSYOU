<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ItemRenderer
		The class for rendering items and its assigned positions.
*/
class ItemRenderer extends PositionRenderer {

	protected $_item;

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

		// set item
		$this->_item = isset($args['item']) ? $args['item'] : null;

		// trigger beforedisplay event
		if ($this->_item) {
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'item:beforedisplay'));
		}

		// render layout
		$result = parent::render($layout, $args);

		// trigger afterdisplay event
		if ($this->_item) {
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'item:afterdisplay', array('html' => &$result)));
		}

		return $result;

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

		$user = $this->app->user->get();
		foreach ($this->_getConfigPosition($position) as $data) {
            if ($element = $this->_item->getElement($data['element'])) {
                if ($element->canAccess($user) && $element->hasValue($this->app->data->create($data))) {
                    return true;
                }
            }
        }

		return false;
	}

	/*
		Function: renderPosition
			Render position output.

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
		$user	  = $this->app->user->get();

		// get style
		$style = isset($args['style']) ? $args['style'] : 'default';

		// store layout
		$layout = $this->_layout;

		// render elements
		foreach ($this->_getConfigPosition($position) as $data) {
            if ($element = $this->_item->getElement($data['element'])) {

				if (!$element->canAccess($user)) {
					continue;
				}

                // set params
                $params = array_merge($data, $args);

                // check value
                if ($element->hasValue($this->app->data->create($params))) {

					// trigger elements beforedisplay event
					$render = true;
					$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforedisplay', array('render' => &$render, 'element' => $element, 'params' => $params)));

					if ($render) {
						$elements[] = compact('element', 'params');
					}
                }
            }
        }

        foreach ($elements as $i => $data) {
            $params  = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements)-1)), $data['params']);

			$output[$i] = parent::render("element.$style", array('element' => $data['element'], 'params' => $params));

			// trigger elements afterdisplay event
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:afterdisplay', array('html' => &$output[$i], 'element' => $data['element'], 'params' => $params)));
        }

		// restore layout
		$this->_layout = $layout;

		return implode("\n", $output);
	}

    protected function _getConfigPosition($position) {
		$config	= $this->getConfig('item')->get($this->_item->getApplication()->getGroup().'.'.$this->_item->getType()->id.'.'.$this->_layout);

        return $config && isset($config[$position]) ? $config[$position] : array();
    }

}