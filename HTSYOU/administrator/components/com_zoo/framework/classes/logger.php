<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppLogger
		Logger base class.
*/
abstract class AppLogger {

	const LEVEL_SUCCESS = 0;
	const LEVEL_INFO    = 1;
	const LEVEL_NOTICE  = 2;
	const LEVEL_WARNING = 3;
	const LEVEL_ERROR   = 4;
	const LEVEL_DEBUG   = 5;

	/* app instance */
	public $app;

	/* log level */
	protected $_level = array();

	/*
		Function: __construct
			Class constructor.
	*/
	public function __construct() {

		// init vars
		$this->_level = array(self::LEVEL_SUCCESS, self::LEVEL_INFO, self::LEVEL_NOTICE, self::LEVEL_WARNING, self::LEVEL_ERROR);
		
	}

	/*
		Function: log
			General log method.
			
		Parameters:
			$level - Log level
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function log($level, $message, $type = null) {
		
		if (in_array($level, $this->_level)) {
			$this->_log($level, $message, $type);
		}

	}

	/*
		Function: success
			Create success log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function success($message, $type = null) {
		$this->log(self::LEVEL_SUCCESS, $message, $type);
	}

	/*
		Function: info
			Create info log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function info($message, $type = null) {
		$this->log(self::LEVEL_INFO, $message, $type);
	}

	/*
		Function: notice
			Create notice log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function notice($message, $type = null) {
		$this->log(self::LEVEL_NOTICE, $message, $type);
	}

	/*
		Function: warning
			Create warning log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function warning($message, $type = null) {
		$this->log(self::LEVEL_WARNING, $message, $type);
	}

	/*
		Function: error
			Create error log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function error($message, $type = null) {
		$this->log(self::LEVEL_ERROR, $message, $type);
	}

	/*
		Function: debug
			Create debug log.
			
		Parameters:
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	public function debug($message, $type = null) {
		$this->log(self::LEVEL_DEBUG, $message, $type);
	}

	/*
		Function: listen
			Event listener callback.
			
		Parameters:
			$event - Event

		Returns:
			Void
	*/
	public function listen($event) {
		$this->log($event['level'], $event['message'], $event['type'] != null ? $event['type'] : 'main');
	}

	/*
		Function: getLevelText
			Get current log level for logger.

		Returns:
			Array
	*/
	public function getLogLevel() {
		return $this->_level;
	}

	/*
		Function: getLevelText
			Set log level for logger.

		Returns:
			Void
	*/
	public function setLogLevel($level) {
		$this->_level = (array) $level;
	}

	/*
		Function: getLevelText
			Get logger level text.

		Returns:
			String
	*/
	public function getLevelText($level) {

		$levels[self::LEVEL_SUCCESS] = JText::_('Success');
		$levels[self::LEVEL_INFO] = JText::_('Info');
		$levels[self::LEVEL_NOTICE] = JText::_('Notice');
		$levels[self::LEVEL_WARNING] = JText::_('Warning');
		$levels[self::LEVEL_ERROR] = JText::_('Error');
		$levels[self::LEVEL_DEBUG] = JText::_('Debug');

		return isset($levels[$level]) ? $levels[$level] : JText::_('Unknown');
	}

	/*
		Function: _log
			Log method, needs to be implemented by subclass.
			
		Parameters:
			$level - Log level
			$message - Log message
			$type - Type of log

		Returns:
			Void
	*/
	abstract protected function _log($level, $message, $type = null);

}