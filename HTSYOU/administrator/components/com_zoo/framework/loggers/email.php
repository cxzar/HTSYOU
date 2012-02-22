<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: EmailLogger
		Email logger class.
*/
class EmailLogger extends AppLogger {

	protected $_format;
	protected $_time;
	protected $_email;
	protected $_log;

	/*
		Function: __construct
			Class constructor.
			
		Parameters:
			$email - Email address
			$format - Log entry format
			$time - Timestamp format
	*/
	public function __construct($email, $format = '%time% %level% %type% %message%%EOL%', $time = '%b %d %H:%M:%S') {
		parent::__construct();

		// init vars
		$this->_format = $format;
		$this->_time = $time;
		$this->_email = $email;
		$this->_log = '';

	}

	/*
		Function: _log
			Write log entry to log text.
			
		Parameters:
			$level - Log level
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
    protected function _log($level, $message, $type = null) {
	
		$this->_log .= strtr($this->_format, array(
	      '%time%'    => strftime($this->_time),
	      '%level%'   => str_pad('['.$this->getLevelText($level).']', 10),
	      '%type%'    => '{'.$type.'}',
	      '%message%' => $message,
	      '%EOL%'     => PHP_EOL,
	    ));

    }

	/*
		Function: __destruct
			Class destructor.
	*/
	public function __destruct() {
	
		// send mail, if log exists
		if ($this->_log) {
			$mail = $this->app->system->mailer;
			$mail->setSubject('Log Message');
			$mail->setBody($this->_log);
			$mail->addRecipient($this->_email);
			$mail->Send();
		}
		
	}

}