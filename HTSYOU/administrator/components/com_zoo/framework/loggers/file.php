<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: FileLogger
		File logger class.
*/
class FileLogger extends AppLogger {

	protected $_format;
	protected $_time;
	protected $_file;
	protected $_fp;

	/*
		Function: __construct
			Class constructor.
			
		Parameters:
			$file - Path to log file
			$format - Log entry format
			$time - Timestamp format
	*/
	public function __construct($file, $format = '%time% %level% %type% %message%%EOL%', $time = '%b %d %H:%M:%S') {
		parent::__construct();

		// init vars
		$this->_format = $format;
		$this->_time = $time;
		$this->_file = $file;
		$this->_fp = fopen($file, 'a');

	}

	/*
		Function: _log
			Write log entry to file.
			
		Parameters:
			$level - Log level
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
    protected function _log($level, $message, $type = null) {
	    flock($this->_fp, LOCK_EX);

	    fwrite($this->_fp, strtr($this->_format, array(
	      '%time%'    => strftime($this->_time),
	      '%level%'   => str_pad('['.$this->getLevelText($level).']', 10),
	      '%type%'    => '{'.$type.'}',
	      '%message%' => $message,
	      '%EOL%'     => PHP_EOL,
	    )));

	    flock($this->_fp, LOCK_UN);
    }

	/*
		Function: __destruct
			Class destructor.
	*/
	public function __destruct() {
	    if (is_resource($this->_fp)) {
	      fclose($this->_fp);
	    }
	}

}