<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ParameterData
		ParameterData Class.
*/
class ParameterData extends JSONData {

	/*
		Function: __construct
			Constructor

		Parameters:
			$data - Array or Object
	*/
	public function __construct($data = array()) {

		if ($data instanceof JRegistry) {
			$data = $data->toArray();
		} else if (is_string($data) && (substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}')) {
			$data = JRegistryFormat::getInstance('INI')->stringToObject($data);
		}

		parent::__construct($data);

	}

	/*
		Function: get
			Get a parameter

		Parameters:
			$name - Name of the parameter
			$default - Default value, return if parameter was not found

		Returns:
			Mixed
	*/
	public function get($name, $default = null) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$values = array();

			foreach ($this as $key => $value) {
				if (strpos($key, $name) === 0) {
					$values[substr($key, strlen($name))] = $value;
				}
			}

			if (!empty($values)) {
				return $values;
			}

		} else if ($this->offsetExists($name)) {
			return $this->offsetGet($name);
		}

		return $default;
	}

	/*
		Function: set
			Set a parameter

		Parameters:
			$name - Name of the parameter
			$value - Value of the parameter

		Returns:
			ParameterData
	*/
	public function set($name, $value) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$values = is_object($value) ? get_object_vars($value) : is_array($value) ? $value : array();

			foreach ($values as $key => $val) {
				$this->offsetSet($name.$key, $val);
			}

		} else {
			$this->offsetSet($name, $value);
		}

		return $this;
	}

	/*
		Function: remove
			Remove a parameter

		Parameters:
			$name - Name of the parameter

		Returns:
			ParameterData
	*/
	public function remove($name) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$keys = array();

			foreach ($this as $key => $value) {
				if (strpos($key, $name) === 0) {
					$keys[] = $key;
				}
			}

			foreach ($keys as $key) {
				$this->offsetUnset($key);
			}

		} else {
			$this->offsetUnset($name);
		}

		return $this;
	}

	/*
		Function: loadArray
			Load a associative array of values

		Parameters:
			$array - Array of values

		Returns:
			ParameterData
	*/
	public function loadArray($array) {

		foreach ($array as $name => $value) {
			$this->offsetSet($name, $value);
		}

		return $this;
	}

	/*
		Function: loadArray
			Load accessible non-static variables of a object

		Parameters:
			$object - Object with values

		Returns:
			ParameterData
	*/
	public function loadObject($object) {

		if (is_object($object)) {
			foreach (get_object_vars($object) as $name => $value) {
				$this->offsetSet($name, $value);
			}
		}

		return $this;
	}

}