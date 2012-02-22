<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: MailHelper
		Mail helper class.
*/
class MailHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppMail', 'classes:mail.php');
	}

	/*
		Function: create
			Retrieve a mail object

		Returns:
			AppMail
	*/
	public function create() {
		return new AppMail($this->app);
	}
	
}