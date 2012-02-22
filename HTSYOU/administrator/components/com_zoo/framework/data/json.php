<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: JSONData
		Read/Write data in JSON format.
*/
class JSONData extends AppData {

    /*
		Variable: _assoc
			Returned object's will be converted into associative array's.
    */
	protected $_assoc = true;

	/*
		Function: __construct
			Constructor
	*/
	public function __construct($data = array()) {

		// decode JSON string
		if (is_string($data)) {
			$data = $this->_read($data);
		}

		parent::__construct($data);
	}

	/*
		Function: _read
			Decode JSON string
	*/
	protected function _read($json = '') {
		return json_decode($json, $this->_assoc);
	}

	/*
		Function: _write
			Encode JSON string
	*/
	protected function _write($data) {
		return $this->_jsonEncode($data);
	}

	/*
		Function: _jsonEncode
			Returns human readable JSON encoded string
	*/
	public function _jsonEncode($in, $indent = 0)	{
		$out = '';

		foreach ($in as $key => $value) {

			$out .= str_repeat("\t", $indent + 1);
			$out .= json_encode((string) $key).': ';

			if (is_object($value) || is_array($value)) {
				$out .= $this->_jsonEncode($value, $indent + 1);
			} else {
				$out .= json_encode($value);
			}

			$out .= ",\n";
		}

		if (!empty($out)) {
			$out = substr($out, 0, -2);
		}

		$out = " {\n" . $out;
		$out .= "\n" . str_repeat("\t", $indent) . "}";

		return $out;
	}
}