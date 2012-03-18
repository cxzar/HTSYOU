<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ParameterFormHelper
		ParmeterForm helper class.
*/
class ParameterFormHelper extends AppHelper {

	/*
		Function: create
			Creates a parameter form instance

		Parameters:
			$type - Parameter form type

		Returns:
			AppParameterForm
	*/
	public function create($args = array()) {
		return $this->app->object->create('AppParameterForm', (array) $args);
	}

	/*
		Function: convertParams
			Convert params to AppData

		Parameters:
			$params - Misc

		Returns:
			AppData
	*/
	public function convertParams($params = array()) {

		if ($params instanceof JParameter) {
			$params = $params->toArray();
		} elseif ($params instanceof AppParameterForm) {
			$params = $params->getValues();
		}

		return $this->app->data->create($params);
	}

}

/*
	Class: AppParameterForm
		Render parameter XML as HTML form.
*/
class AppParameterForm {

    /*
		Variable: app
			App instance.
    */
	public $app;

	/*
		Variable: _values
			Array of values.
    */
	protected $_values = array();

	/*
		Variable: xml
			The xml params object array, with each group as array key.
    */
	protected $_xml;

	/*
		Function: __construct
			Constructor
	*/
	public function __construct($xml = null) {

		// get zoo app instance
		$this->app = App::getInstance('zoo');

		// init vars
		$this->loadXML($xml);

	}

	/*
		Function: getValue
			Retrieve a form value

		Return:
			Mixed
	*/
	public function getValue($name, $default = null) {

		if (isset($this->_values[$name])) {
			return $this->_values[$name];
		}

		return $default;
	}

	/*
		Function: setValue
			Set a form value

		Return:
			AppParameterForm
	*/
	public function setValue($name, $value) {
		$this->_values[$name] = $value;
		return $this;
	}

	/*
		Function: getValues
			Retrieve form values

		Return:
			Array
	*/
	public function getValues() {
		return $this->_values;
	}

	/*
		Function: setValues
			Set form values

		Parameters:
			values - ParameterData, Array, Object

		Return:
			AppParameterForm
	*/
	public function setValues($values) {
		$this->_values = (array) $values;
		return $this;
	}

	/*
		Function: addElementPath
			Add a directory to search for field types

		Parameters:
			path - Element path (string)
	*/
	public function addElementPath($path) {
		$this->app->path->register($path, 'fields');
		return $this;
	}

	/*
		Function: getParamsCount
			Return number of params to render

		Parameters:
			group - Parameter group

		Returns:
			Int - Parameter count
	*/
	public function getParamsCount($group = '_default') {
		if (!isset($this->_xml[$group]) || !count($this->_xml[$group]->children())) {
			return false;
		}

		return count($this->_xml[$group]->children());
	}

	/*
		Function: getGroups
			Get the number of params in each group

		Returns:
			Array - Array of all group names as key and parameter count as value
	*/
	public function getGroups() {
		if (!is_array($this->_xml)) {
			return false;
		}

		$results = array();

		foreach ($this->_xml as $name => $group)  {
			$results[$name] = $this->getParamsCount($name);
		}

		return $results;
	}

	/*
		Function: setXML
			Sets the XML object from custom xml files

		Parameters:
			xmlpath - Path to xml file

		Returns:
			Boolean - True, on success
	*/
	public function setXML($xml) {
		if ($xml instanceof SimpleXMLElement) {

			if ($group = (string) $xml->attributes()->group) {
				$this->_xml[$group] = $xml;
			} else {
				$this->_xml['_default'] = $xml;
			}

			if ($path = (string) $xml->attributes()->addpath) {
				$this->addElementPath(JPATH_ROOT.$path);
			}
		}
	}

	/*
		Function: loadXML
			Loads an xml file or formatted string and parses it

		Parameters:
			data - xml file or string

		Returns:
			Boolean - True, on success
	*/
	public function loadXML($data) {

		// load xml file or string ?
		if (($xml = @simplexml_load_file($data)) || ($xml = simplexml_load_string($data))) {
			if (isset($xml->params)) {
				foreach ($xml->params as $param) {
					$this->setXML($param);
				}

				return true;
			}
		}

		return false;
	}

	/*
		Function: addXML
			Adds an xml file or formatted string and parses it

		Parameters:
			data - xml file or string

		Returns:
			Boolean - True, on success
	*/
	public function addXML($data) {

		// load xml file or string ?
		if (($xml = @simplexml_load_file($data)) || ($xml = simplexml_load_string($data))) {
			if (isset($xml->params)) {
				foreach ($xml->params as $params) {

					$group = $params->attributes()->group ? (string) $params->attributes()->group : '_default';

					if (!isset($this->_xml[$group])) {
						$this->_xml[$group] = new AppSimpleXMLElement('<params></params>');
					}

					foreach ($params->param as $param) {
						$this->_xml[$group]->appendChild($param);
					}

					if ($path = (string) $params->attributes()->addpath) {
						$this->addElementPath(JPATH_ROOT.$path);
					}

				}

				return true;
			}
		}

		return false;
	}

	/*
		Function: getXML
			Get the xml for a specific group or all groups

		Parameters:
			$group - the group to return

		Returns:
			Mixed - Array of groups or the xml for a group
	*/
	public function getXML($group = null) {

		if (!$group) {
			return $this->_xml;
		}

		if (isset($this->_xml[$group])) {
			return $this->_xml[$group];
		}

		return false;
	}

	/*
		Function: render
			Render parameter HTML form

		Parameters:
			name - The name of the control, or the default text area if a setup file is not found
			group - Parameter group

		Returns:
			String - HTML
	*/
	public function render($control_name = 'params', $group = '_default') {
		if (!isset($this->_xml[$group])) {
			return false;
		}

		$html = array('<ul class="parameter-form">');

		// add group description
		if ($description = (string) $this->_xml[$group]->attributes()->description) {
			$html[]	= '<li class="description">'.JText::_($description).'</li>';
		}

		// add params
		foreach ($this->_xml[$group]->param as $param) {

			// init vars
			$type  = (string) $param->attributes()->type;
			$name  = $param->attributes()->name;
			$value = $this->getValue((string) $param->attributes()->name, (string) $param->attributes()->default);

			$html[] = '<li class="parameter">';

			$output = '&#160;';
			if ((string) $param->attributes()->label != '') {
				$attributes = array('for' => $control_name.$name);
				if ((string) $param->attributes()->description != '') {
					$attributes['class'] = 'hasTip';
					$attributes['title'] = JText::_($param->attributes()->label) . '::' . JText::_($param->attributes()->description);
				}
				$output = sprintf ('<label %s>%s</label>', $this->app->field->attributes($attributes), JText::_($param->attributes()->label));
			}

			$html[] = "<div class=\"label\">$output</div>";
			$html[] = '<div class="field">'.$this->app->field->render($type, $name, $value, $param, array('control_name' => $control_name, 'parent' => $this)).'</div>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		return implode("\n", $html);
	}

}

class AppSimpleXMLElement extends SimpleXMLElement {

	public function appendChild($append) {
        if ($append) {
            if (strlen(trim((string) $append))==0) {
                $xml = $this->addChild($append->getName());
                foreach($append->children() as $child) {
                    $xml->appendChild($child);
                }
            } else {
                $xml = $this->addChild($append->getName(), (string) $append);
            }
            foreach($append->attributes() as $n => $v) {
                $xml->addAttribute($n, $v);
            }
        }
    }

}