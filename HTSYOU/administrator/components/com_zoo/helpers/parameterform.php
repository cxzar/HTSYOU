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
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:parameterform'), 'parameterform');

		// load class
		$this->app->loader->register('JSimpleXMLElement', 'root:libraries/joomla/utilities/simplexml.php');
		$this->app->loader->register('AppParameterFormXML', 'parameterform:xml.php');
		$this->app->loader->register('AppParameterFormDefault', 'parameterform:default.php');
	}

	/*
		Function: create
			Creates a parameter form instance

		Parameters:
			$type - Parameter form type

		Returns:
			AppParameterForm
	*/
	public function create($args = array(), $type = 'default') {

		$args = (array) $args;
		$class = 'AppParameterForm' . $type;

		return $this->app->object->create($class, $args);

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
abstract class AppParameterForm {

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
		Variable: _elements
			Elements.
    */
	protected $_elements = array();

	/*
		Variable: _resource
			The path resource.
    */
	protected $_resource = 'joomla.elements';

	/*
		Function: __construct
			Constructor
	*/
	public function __construct() {

		// get zoo app instance
		$this->app = App::getInstance('zoo');

		// set default element paths
		$this->addElementPath(JPATH_LIBRARIES.'/joomla/html/parameter/element');
		$this->addElementPath(dirname(__FILE__).'/element');
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

		if ($values instanceof ParameterData) {
			$this->_values = (array) $values;
		} else if (is_array($values)) {
			$this->_values = $values;
		} else if (is_object($values)) {
			$this->_values = get_object_vars($values);
		}

		return $this;
	}

	/*
		Function: loadElement
			Loads a element type

		Parameters:
			type - Element type
	*/
	public function loadElement($type, $new = false) {
		$signature = md5($type);

		if ((isset($this->_elements[$signature]) && !$this->_elements[$signature] instanceof __PHP_Incomplete_Class) && $new === false) {
			return $this->_elements[$signature];
		}

		$elementClass = 'JElement'.$type;

		if(!class_exists($elementClass)) {
			$file = $this->app->object->create('JFilterInput')->clean(str_replace('_', DS, $type).'.php', 'path');
			if ($elementFile = $this->app->path->path($this->_resource.':'.$file)) {
				include_once $elementFile;
			}
		}

		if (!class_exists($elementClass)) {
			return false;
		}

		$this->_elements[$signature] = $this->app->object->create($elementClass, array($this));

		return $this->_elements[$signature];
	}

	/*
		Function: addElementPath
			Add a directory to search for element types

		Parameters:
			path - Element path (string or array)
	*/
	public function addElementPath($path) {
		$this->app->path->register($path, $this->_resource);
		return $this;
	}

	/*
		Function: render
			Render parameter html
	*/
	abstract public function render();

}