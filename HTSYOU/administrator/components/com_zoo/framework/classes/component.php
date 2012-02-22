<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppComponent
		Access components configuration.
*/
class AppComponent {

	public $app;
	public $name;
	protected $_component;
	protected $_params;

	/*
		Function: __construct
			Constructor
 	*/
	public function __construct($app, $name) {
		$this->app        = $app;
		$this->name       = $name;
		$this->_component = JComponentHelper::getComponent($name);
		$this->_params    = $app->parameter->create($this->_component->params);
	}

	/*
		Function: link
			Get link to component related resources.

		Parameters:
			$query - HTTP query options
			$xhtml - Replace & by &amp; for xml compilance
			$ssl - Secure state for the resolved URI
		            1: Make URI secure using global secure site URI
		  		    0: Leave URI in the same secure state as it was passed to the function
		  		   -1: Make URI unsecure using the global unsecure site URI

		Returns:
			String
	*/
	public function link($query = array(), $xhtml = true, $ssl = null) {

		// prepend option to query
		$query = array_merge(array('option' => $this->name), $query);

		return JRoute::_('index.php?'.http_build_query($query, '', '&'), $xhtml, $ssl);
	}

	/*
		Function: get
			Returns a configuration property of the object or the default value if the property is not set.

		Parameters:
			$property - The name of the property
			$default - The default value

		Returns:
			Mixed - The value of the property.
	*/
	public function get($property, $default = null) {
		return $this->_params->get($property, $default);
	}

	/*
		Function: set
			Modifies a property of the object, creating it if it does not already exist.

		Parameters:
			$property - The name of the property
			$value - The value of the property to set

		Returns:
			Mixed - Previous value of the property.
	*/
	public function set($property, $value = null) {
		return $this->_params->set($property, $value);
	}

	/*
		Function: save
			Save configuration properties.

		Returns:
			Void
	*/
	public function save() {

		// init vars
		$table     = $this->app->table->get('components', '#__');
		$component = $table->get($this->_component->id);

		// save properties
		$component->params = $this->_params->toString();
		$table->save($component);
	}

}