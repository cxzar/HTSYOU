<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: LogHelper
		Logging helper class.
*/
class LogHelper extends AppHelper {

	protected $_event = 'app:log';

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppLogger', 'classes:logger.php');
	}

	public function success($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_SUCCESS, $message, $type);
	}

	public function info($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_INFO, $message, $type);
	}

	public function notice($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_NOTICE, $message, $type);
	}

	public function warning($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_WARNING, $message, $type);
	}

	public function error($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_ERROR, $message, $type);
	}

	public function debug($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_DEBUG, $message, $type);
	}

	public function createLogger($type, $args = array()) {
		
		// load data class
		$class = $type.'Logger';
		$this->app->loader->register($class, 'loggers:'.strtolower($type).'.php');

		// use reflection for logger creation
		if (count($args) > 0) {
			$reflection = new ReflectionClass($class);
			$logger = $reflection->newInstanceArgs($args);
		} else {
			$logger = new $class();
		}

		return $this->addLogger($logger);
	}

	public function addLogger($logger) {
		
		// set app
		$logger->app = $this->app;
		
		// add logger to application log event
		$this->app->event->dispatcher->connect($this->_event, array($logger, 'listen'));

		return $logger;
	}

	public function removeLogger($logger) {
		
		// remove logger from application log event
		$this->app->event->dispatcher->disconnect($this->_event, array($logger, 'listen'));

		return $logger;
	}

	protected function _notify($level, $message, $type = null) {

		// auto-detect type
		if ($type == null) {
			
			// get backtrace
			$backtrace = debug_backtrace();
			if (isset($backtrace[2]['class'])) {
				$type = $backtrace[2]['class'];
			} elseif (isset($backtrace[2]['object'])) {
				$type = get_class($backtrace[2]['object']);
			}

		}

		// fire event
	    $this->app->event->dispatcher->notify($this->app->event->create($this, $this->_event, compact('level', 'message', 'type')));
		
	}

}