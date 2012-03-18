<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: App
		App framework class.
*/
class App {

	/* framework version */
    const VERSION = '1.0.0';

	/* unique identifier */
    public $id;

	/* helpers */
	protected $_helpers = array();

	/* instances */
	protected static $_instances = array();

	/*
		Function: __construct
			Class Constructor.

		Parameters:
			$id - Unique identifier

		Returns:
			App
	*/
	public function __construct($id) {

		// init vars
		$this->id = $id;

		// set defaults
		$path = dirname(dirname(__FILE__));
		$this->addHelper(new PathHelper($this));
		$this->addHelper(new UserAppHelper($this));
		$this->path->register(JPATH_ROOT, 'root');
		$this->path->register(JPATH_ROOT.'/media', 'media');
		$this->path->register($path.'/classes', 'classes');
		$this->path->register($path.'/data', 'data');
		$this->path->register($path.'/helpers', 'helpers');
		$this->path->register($path.'/loggers', 'loggers');

	}

	/*
		Function: getInstance
			Retrieve app instance

		Parameters:
			$id - Unique identifier

		Returns:
			App
	*/
	public static function getInstance($id) {

		// add instance, if not exists
		if (!isset(self::$_instances[$id])) {
			self::$_instances[$id] = new App($id);
		}

		return self::$_instances[$id];
	}

	/*
		Function: getHelper
			Retrieve a helper

		Parameters:
			$name - Helper name
	*/
	public function getHelper($name) {

		// try to load helper, if not found
		if (!isset($this->_helpers[$name])) {
		    $this->loadHelper($name);
		}

		// get helper
		if (isset($this->_helpers[$name])) {
			return $this->_helpers[$name];
		}

		return null;
	}

	/*
		Function: addHelper
			Adds a helper

		Parameters:
			$helper - Helper object
			$alias - Helper alias (optional)
	*/
	public function addHelper($helper, $alias = null) {

		// add to helpers
		$name = $helper->getName();
		$this->_helpers[$name] = $helper;

		// add alias
		if (!empty($alias)) {
			$this->_helpers[$alias] = $helper;
		}

	}

	/*
		Function: loadHelper
			Load helper from path

		Parameters:
			$helpers - Helper names
			$suffix - Helper class suffix
	*/
	public function loadHelper($helpers, $suffix = 'Helper') {
		$helpers = (array) $helpers;

		foreach ($helpers as $name) {
			$class = $name.$suffix;

			// autoload helper class
			if (!class_exists($class) && ($file = $this->path->path('helpers:'.$name.'.php'))) {
			    require_once($file);
			}

			// add helper, if not exists
			if (!isset($this->_helpers[$name])) {
				$this->addHelper(new $class($this));
			}
		}
	}

	/*
		Function: __get
			Retrieve a helper

		Parameters:
			$name - Helper name

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->getHelper($name);
	}

	/*
		Function: link
			Get link to this component's related resources.

		Returns:
			String
	*/
	public function link($query = array(), $xhtml = true, $ssl = null) {
		return $this->component->{$this->id}->link($query, $xhtml, $ssl);
	}

	/*
		Function: get
			Get a configuration property of this component.

		Returns:
			Mixed - The value of the property.
	*/
	public function get($property, $default = null) {
		return $this->component->{$this->id}->get($property, $default);
	}

	/*
		Function: set
			Set a configuration property of this component.

		Returns:
			Mixed - Previous value of the property.
	*/
	public function set($property, $value = null) {
		return $this->component->{$this->id}->set($property, $value);
	}

	/*
		Function: dispatch
			Dispatch app controller.

		Parameters:
			$default - Default controller name
			$config - Additional config options

		Returns:
			Void.
	*/
	public function dispatch($default = null, $config = array()) {

		// init vars
		$controller = $this->request->get('controller', 'word');
		$task       = $this->request->get('task', 'cmd');

		// load controller
		if ($file = $this->path->path('controllers:'.$controller.'.php')) {
			require_once($file);
		} elseif ($default != null) {
			$controller = $default;
			if ($file = $this->path->path('controllers:'.$controller.'.php')) {
				require_once($file);
			}
		}

		// controller loaded ?
		$class = $controller.'Controller';
		if (class_exists($class)) {

			// perform the request task
			$ctrl = new $class($this, $config);
			$ctrl->execute($task);
			$ctrl->redirect();

		} else {
			throw new AppException("Controller class not found. ($class)");
		}

	}

}

/*
	Class: AppException
*/
class AppException extends Exception {

	public function __toString() {
		return $this->getMessage();
	}

}