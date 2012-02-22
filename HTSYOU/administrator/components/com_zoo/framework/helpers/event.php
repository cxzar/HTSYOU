<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: EventHelper
		Event helper class. Create and manage Events.
*/
class EventHelper extends AppHelper {

	/* dispatcher */
	protected static $_dispatcher;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppEvent', 'classes:event.php');
		$this->app->loader->register('AppEventDispatcher', 'classes:event.php');

		// set dispatcher
		if (!isset(self::$_dispatcher)) {
			self::$_dispatcher = new AppEventDispatcher();
		}

	}

	/*
		Function: register
			Register event class.

		Parameters:
			$class - Class name
			$file - File name

		Returns:
			Void
	*/
	public function register($class, $file = null) {
		
		if ($file == null) {
			$file = 'events:'.basename(strtolower($class), 'event').'.php';
		}
	
		return $this->app->loader->register($class, $file);
	}

	/*
		Function: create
			Create new Event

		Returns:
			Event
 	*/
	public static function create($subject, $name, $parameters = array()) {
		return new AppEvent($subject, $name, $parameters);
	}
	
	/*
		Function: __get
			Retrieve protected variables (dispatcher)

		Parameters:
			$name - Variable name

		Returns:
			Mixed
	*/	
	public function __get($name) {
		
		if ($name == 'dispatcher') {
			return self::$_dispatcher;
		}
		
		return null;
	}
	
}