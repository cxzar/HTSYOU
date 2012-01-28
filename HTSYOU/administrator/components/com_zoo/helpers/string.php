<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: StringHelper
		The general String Helper.
*/
class StringHelper extends AppHelper {

	/* wrapped class */
	protected $_class = 'JString';

	/*
		Function: __call
			Map all functions to JRequest class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/
    public function __call($method, $args) {
		return $this->_call(array($this->_class, $method), $args);
    }

	/*
		Function: truncate
			Truncates the input string.

		Parameters:
			$text - input string
			$length - the length of the output string
			$truncate_string - the truncate string

		Returns:
			String - truncated string
	*/
	public function truncate($text, $length = 30, $truncate_string = '...') {

		if ($text == '') {
			return '';
		}

		if ($this->strlen($text) > $length) {
			$length -= min($length, strlen($truncate_string));
			$text  = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length + 1));

			return $this->substr($text, 0, $length) . $truncate_string;

		} else {
			return $text;
		}
	}

	/*
		Function: sluggify
			Sluggifies the input string.

		Parameters:
			$string - input string

		Returns:
			String - sluggified string
	*/
	public function sluggify($string) {

		$special = array('\'','À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','A','a','A','a','C','c','C','c','Ç','ç','Č','č','D','d','Ð','d', 'È','è','É','é','Ê','ê','Ë','ë','E','e','E','e', 'G','g','Ì','ì','Í','í','Î','î','Ï','ï', 'L','l','L','l','L','l', 'Ñ','ñ','N','n','N','n','Ò','ò','Ó','ó','Ô','ô','Õ','õ','Ö','ö','Ø','ø','o','R','r','R','r','Š','š','S','s','S','s', 'T','t','T','t','T','t','Ù','ù','Ú','ú','Û','û','Ü','ü','U','u', 'Ÿ','ÿ','ý','Ý','Ž','ž','Z','z','Z','z', 'Þ','þ','Ð','ð','ß','Œ','œ','Æ','æ','µ','Ğ','Ü','Ş','Ö','Ç','İ','ğ','ü','ş','ö','ç','ı');
		$standard = array('-','A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d', 'E','e','E','e','E','e','E','e','E','e','E','e','G','g','I','i','I','i','I','i','I','i','L','l','L','l','L','l', 'N','n','N','n','N','n', 'O','o','O','o','O','o','O','o','Oe','oe','O','o','o', 'R','r','R','r', 'S','s','S','s','S','s','T','t','T','t','T','t', 'U','u','U','u','U','u','Ue','ue','U','u','Y','y','Y','y','Z','z','Z','z','Z','z','TH','th','DH','dh','ss','OE','oe','AE','ae','u','g','u','s','o','c','i','g','u','s','o','c','i');

		$string = (string) $string;

		foreach ($special as $i => $value) {
			$string = str_replace($value, $standard[$i], $string);
		}

		$string = preg_replace(array('/\s+/','/[^\x{00C0}-\x{00D6}x{00D8}-\x{00F6}\x{00F8}-\x{00FF}\x{0370}-\x{1FFF}\x{4E00}-\x{9FAF}a-z0-9\-]/ui'), array('-',''), $string);
		$string = preg_replace('/[-]+/u', '-', $string);
		$string = trim($string, '-');

		return trim($this->strtolower($string));

	}

}