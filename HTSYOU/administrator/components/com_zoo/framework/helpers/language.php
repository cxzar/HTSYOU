<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: LanguageHelper
		Language helper class. Wrapper for JLanguage/JText.
*/
class LanguageHelper extends AppHelper {

	/*
		Function: l
			Translates a string into the current language

		Parameters:
			$string - String to translate
			$js_safe - Make the result javascript safe

		Returns:
			Mixed
	*/	
	public function l($string, $js_safe = false) {
		return $this->app->system->language->_($string, $js_safe);
	}
	
}