<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: UtilityHelper
		The general Utility Helper.
*/
class UtilityHelper extends AppHelper {

	/*
		Function: generateUUID
			Generates a universally unique identifier (UUID v4) according to RFC 4122
			Version 4 UUIDs are pseudo-random.

		Returns:
			String
	*/
	public function generateUUID() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
	}

	/*
		Function: debugInfo
			Retreive debug information from debug trace array.

		Returns:
			String
	*/
	public function debugInfo($trace, $index = 0) {

		if (isset($trace[$index])) {
			$file = $this->app->path->relative($trace[$index]['file']);
			$line = $trace[$index]['line'];

			return sprintf('File: %s, Line: %s', $file, $line);
		}

		return null;
	}

	/*
		Function: returnBytes
			Translates PHP ini's shorthand notation to bytes

		Returns:
			String
	*/
	public function returnBytes ($size_str) {
	    switch (substr ($size_str, -1)) {
	        case 'M': case 'm': return (int)$size_str * 1048576;
	        case 'K': case 'k': return (int)$size_str * 1024;
	        case 'G': case 'g': return (int)$size_str * 1073741824;
	        default: return $size_str;
	    }
	}

}