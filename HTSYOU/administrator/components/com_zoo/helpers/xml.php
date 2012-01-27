<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class XMLHelper extends AppHelper{

	/*
		Function: loadFile
			Interprets an XML file into an object.

		Parameters:
			$file - filename string

		Returns:
			SimpleXMLElement, false on failure
	*/
	public function loadFile($file) {

        if (JFile::exists($file)) {
            $data = JFile::read($file);

            return $this->loadString($data);

        }

		return null;

	}

	/*
		Function: loadString
			Interprets a string of XML into an object.

		Parameters:
			$string - data string

		Returns:
			SimpleXMLElement, false on failure
	*/
	public function loadString($string) {

		libxml_use_internal_errors(true);

        $string = $this->stripInvalidXMLCharacters($string);

		return simplexml_load_string($string);

	}

	/*
		Function: stripInvalidXMLCharacters
			Strips invalid xml characters from a string (according to http://www.w3.org/TR/REC-xml/#charsets)

		Parameters:
			$string - data string

		Returns:
			String, cleaned string
	*/
    public function stripInvalidXMLCharacters($string = '') {
        return preg_replace('/[^\x09\x0A\x0D\x20-\xD7FF\xE000-\xFFFD]/', '', $string);
    }

}