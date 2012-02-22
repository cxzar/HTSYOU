<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppMail
		The mailer class, to send emails.
*/
class AppMail {

	/* app instance */
	public $app;

	/* mail object */
	protected $_mail;

	/*
		Function: __construct
			Class constructor.
	*/
	public function __construct($app) {

		// init vars
		$this->app   = $app;
		$this->_mail = $app->system->mailer;

	}

	/*
		Function: setBody
			Set mail body.
			
		Parameters:
			$content - Text

		Returns:
			Void
	*/
	public function setBody($content) {

		// auto-detect html
		if (stripos($content, '<html') !== false) {
			$this->_mail->IsHTML(true);
		}

		// set body
		$this->_mail->setBody($content);
	}

	/*
		Function: setBodyFromTemplate
			Set mail body using a template file.
			
		Parameters:
			$template - Path to template file
			$args - Arguments to pass on to template

		Returns:
			Void
	*/
	public function setBodyFromTemplate($template, $args = array()) {

		// init vars
		$__tmpl = $this->app->path->path($template);

		// does the template file exists ?
		if ($__tmpl == false) {
			throw new AppMailException("Mail Template $template not found");
		}

		// render the mail template
		extract($args);
		ob_start();
		include($__tmpl);
		$output = ob_get_contents();
		ob_end_clean();

		// set body
		$this->setBody($output);
	}
	
	/*
		Function: __call
			Map all functions to mail object

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/	
    public function __call($method, $args) {
        return call_user_func_array(array($this->_mail, $method), $args);
    }

}

/*
	Class: AppMailException
*/
class AppMailException extends AppException {}