<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class BlogApplication extends Application {

	/*
		Function: dispatch
			Dispatch application through executing the current controller.

		Returns:
			Void
	*/
	public function dispatch() {
		$this->app->path->register($this->getTemplate()->getPath().'/classes', 'classes');
		parent::dispatch();
	}

}