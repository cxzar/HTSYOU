<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: Type
		Type related attributes and functions.
*/
class Type {

    /*
       Variable: id
         Type id.
    */
	public $id;

    /*
       Variable: identifier
         Type unique identifier.
    */
	public $identifier;

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
		Variable: config
			AppData config.
    */
	public $config;

    /*
       Variable: _application
         Related application.
    */
	protected $_application;

    /*
       Variable: _elements
         Element objects.
    */
	protected $_elements;

    /*
       Variable: _core_elements_config
         Core element config.
    */
	protected $_core_elements_config;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      id - Type id
	      basepath - Type base path

		Returns:
		  Type
 	*/
	public function __construct($id, $application = null) {

		// init vars
		$this->app = App::getInstance('zoo');

		// init vars
		$this->id = $id;
		$this->identifier = $id;
		$this->_application = $application;

		$this->config = $this->app->data->create(JFile::exists($this->getConfigFile()) ? JFile::read($this->getConfigFile()) : null);

	}

	/*
		Function: getApplication
			Retrieve related application object.

		Returns:
			Application
	*/
	public function getApplication() {
		return $this->_application;
	}

	/*
		Function: getName
			Retrieve types name.

		Returns:
			String
	*/
	public function getName() {
		return $this->name;
	}

	/*
    	Function: getElement
    	  Get element object by name.

	   	  Returns:
	        Object
 	*/
	public function getElement($identifier) {

		// has element already been loaded?
		if (!$element = isset($this->_elements[$identifier]) ? $this->_elements[$identifier] : null) {
			if ($config = $this->getElementConfig($identifier)) {
				if ($element = $this->app->element->create((string) $config->type, $this->_application)) {
					$element->identifier = $identifier;
					$element->config = $config;
					$this->_elements[$identifier] = $element;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		$element = clone($element);
		$element->setType($this);

		return $element;
	}

	/*
    	Function: getElementConfig
    	  Get element config by element identifier.

	   	  Returns:
	        AppData - Elements config
 	*/
	public function getElementConfig($identifier) {

		if (isset($this->elements[$identifier])) {
			return $this->app->data->create($this->elements[$identifier]);
		}

		$core_config = $this->getCoreElementsConfig();
		if (isset($core_config[$identifier])) {
			return $this->app->data->create($core_config[$identifier]);
		}

		return null;
	}

	/*
    	Function: getElements
    	  Get all element objects from the parsed type elements xml.

	   	  Returns:
	        Array - Array of element objects
 	*/
	public function getElements() {
		$identifiers = $this->elements ? array_keys($this->elements) : array();
		return $this->_getElements(array_filter($identifiers, create_function('$a', 'return strpos($a, \'_item\') !== 0;')));
	}

	/*
    	Function: getCoreElements
    	  Get all core element objects.

	   	  Returns:
	        Array - Array of element objects
 	*/
	public function getCoreElements() {
		return $this->_getElements(array_keys($this->getCoreElementsConfig()));
	}

	protected function _getElements($identifiers) {
		if ($identifiers) {
			$elements = array();
			foreach ($identifiers as $identifier) {
				if ($element = $this->getElement($identifier)) {
					$elements[$identifier] = $element;
				}
			}
			return $elements;
		}

		return array();
	}

	public function getCoreElementsConfig() {
		if (!$this->_core_elements_config) {
			$config = $this->app->data->create(JFile::read($this->app->path->path('elements:core.config')))->get('elements');
			$this->_core_elements_config = $this->app->event->dispatcher->notify($this->app->event->create($this, 'type:coreconfig')->setReturnValue($config))->getReturnValue();
		}

		return $this->_core_elements_config;
	}

	/*
    	Function: getSubmittableElements
    	  Get all submittable element objects from the parsed type elements xml.

	   	  Returns:
	        Array - Array of submittable element objects
 	*/
	public function getSubmittableElements() {
		return	array_filter(array_merge($this->getElements(), $this->getCoreElements()), create_function('$element', 'return $element instanceof iSubmittable;'));
	}

	/*
    	Function: clearElements
    	  Clear loaded elements object.

	   	  Returns:
	        Type
 	*/
	public function clearElements() {
		$this->_elements = null;
		return $this;
	}

	/*
		Function: getConfigFile
			Retrieve config file.

		Returns:
			String
	*/
	public function getConfigFile($id = null) {

		$id = ($id !== null) ? $id : $this->id;

		if ($id && ($path = $this->app->path->path($this->_application->getResource().'types'))) {
			return $path.'/'.$id.'.config';
		}

		return null;

	}

	/*
		Function: bind
			Bind data array to type.

		Returns:
			Type
	*/
	public function bind($data) {

		if (isset($data['identifier'])) {

			// check identifier
			if ($data['identifier'] == '' || $data['identifier'] != $this->app->string->sluggify($data['identifier'])) {
				throw new TypeException('Invalid identifier');
			}

			$this->identifier = $data['identifier'];
		}

		if (isset($data['name'])) {

			// check name
			if ($data['name'] == '') {
				throw new TypeException('Invalid name');
			}

			$this->name = $data['name'];
		}

		return $this;
	}

	/*
		Function: bindElements
			Bind data array to type elements.

		Returns:
			Type
	*/
	public function bindElements($data) {

		if (isset($data['elements'])) {
			$this->elements = $data['elements'];
		}

		$this->clearElements();
		return $this;
	}

	/*
		Function: save
			Save type data.

		Returns:
			Type
	*/
	public function save() {

		$old_identifier = $this->id;
		$rename = false;

		if (empty($this->id)) {

			// check identifier
			if (file_exists($this->getConfigFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

		} else if ($old_identifier != $this->identifier) {

			// check identifier
			if (file_exists($this->getConfigFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

			// rename xml file
			if (!JFile::move($this->getConfigFile(), $this->getConfigFile($this->identifier))) {
				throw new TypeException('Renaming config file failed');
			}

			$rename = true;

		}

		// update id
		$this->id = $this->identifier;

		// save config file
		if ($file = $this->getConfigFile()) {
			$config_string = (string) $this->config;
			if (!JFile::write($file, $config_string)) {
				throw new TypeException('Writing type config file failed');
			}
		}

		// rename related items
		if ($rename) {

			// get database
			$db = $this->app->database;

			$group = $this->getApplication()->getGroup();

			// update childrens parent category
			$query = "UPDATE ".ZOO_TABLE_ITEM." as a, ".ZOO_TABLE_APPLICATION." as b"
			    	." SET a.type=".$db->quote($this->identifier)
				    ." WHERE a.type=".$db->quote($old_identifier)
					." AND a.application_id=b.id"
					." AND b.application_group=".$db->quote($group);
			$db->query($query);
		}

		return $this;
	}

	/*
		Function: delete
			Delete type data.

		Returns:
			Type
	*/
	public function delete() {

		// check if type has items
		if ($this->app->table->item->getTypeItemCount($this)) {
			throw new TypeException('Cannot delete type, please delete the related items first');
		}

		// delete config file
		if (!JFile::delete($this->getConfigFile())) {
			throw new TypeException('Deleting config file failed');
		}

		return $this;
	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->config->has($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->config->get($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed

		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->config->set($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Void
	*/
	public function __unset($name) {
		$this->config->remove($name);
	}

}

/*
	Class: TypeException
*/
class TypeException extends AppException {}