<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DocumentHelper
		Document helper class. Wrapper for JDocument.
*/
class DocumentHelper extends AppHelper {

	private $file_mod_date;

	/*
		Function: addStylesheet
			Add stylesheet to the document.

		Parameters:
			$path - Path to css file
			$version - version to attach, if null date will be used

		Returns:
			Void
	*/
	public function addStylesheet($path, $version = null) {
		if ($file = $this->app->path->url($path)) {
			$this->app->system->document->addStylesheet($file.$this->getVersion($version));
		}
	}

	/*
		Function: script
			Add javascript to the document.

		Parameters:
			$path - Path to js file
			$version - version to attach, if null date will be used

		Returns:
			Void
	*/
	public function addScript($path, $version = null) {

		$version = $this->getVersion($version);

		// load jQuery, if not loaded before
		if (!$this->app->system->application->get('jquery')) {
			$this->app->system->application->set('jquery', true);
			$this->app->system->document->addScript($this->app->path->url('libraries:jquery/jquery.js').$version);
		}

		if ($file = $this->app->path->url($path)) {
			$this->app->system->document->addScript($file.$version);
		}
	}

	/*
		Function: __call
			Map all functions to JDocument class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/
    public function __call($method, $args) {
		return $this->_call(array($this->app->system->document, $method), $args);
    }

	private function getVersion($version = null) {

		if ($version === null) {
			if (empty($this->file_mod_date)) {
				$this->file_mod_date = date("Ymd", filemtime(__FILE__));
			}

			return '?ver='.$this->file_mod_date;
		}

		return empty($version) ? '' : '?ver='.$version;
	}

}