<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppTable
		The table class, to load objects from database.
*/
class AppTable {

	public $app;
	public $name;
	public $key;
	public $class;
	public $fields;
	public $database;
	protected $_objects = array();

	public function __construct($app, $name, $key = 'id') {

		// init vars
		$this->app = $app;
		$this->name = $name;
		$this->key = $key;
		$this->database = $app->database;

		// set default class name
		$this->class = get_class($this) == __CLASS__ ? 'stdClass' : basename(get_class($this), 'Table');

		// load class
		$this->app->loader->register($this->class, 'classes:'.strtolower($this->class).'.php');
	}

	public function getTableFields() {

		if (empty($this->fields)) {
			$this->fields = array_shift($this->database->getTableFields($this->name));
		}

		return $this->fields;
	}

	public function get($key, $new = false) {
		$options = array('conditions' => array($this->key.' = ?', $key));

		// get new object
		if ($new) {
			return $this->find('first', $options);
		}

		// get saved object instance
		if (!isset($this->_objects[$key])) {
			$this->_objects[$key] = $this->find('first', $options);
		}

		return $this->_objects[$key];
	}

	public function first($options = null) {
		return $this->find('first', $options);
	}

	public function all($options = null) {
		return $this->find('all', $options);
	}

	public function find($mode = 'all', $options = null) {

		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);

		if ($mode == 'first') {
			return $this->_queryObject($query);
		}

		return $this->_queryObjectList($query);
	}

	public function count($options = null) {

		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);

		$this->database->query($query);

		return $this->database->getNumRows();
	}

	public function save($object) {

		// init vars
		$vars   = get_object_vars($object);
		$fields = $this->getTableFields();

		foreach ($fields as $key => $value) {
			$fields[$key] = array_key_exists($key, $vars) ? (string) $vars[$key] : null;
		}

		// insert or update database
		$obj = (object) $fields;
		$key = $this->key;
		if ($obj->$key) {

			// update object
			$this->database->updateObject($this->name, $obj, $key);

		} else {

			// insert object
			$this->database->insertObject($this->name, $obj, $key);

			// set insert id
			$object->$key = $obj->$key;
		}

	}

	public function delete($object) {

		// get table key
		$key = $this->key;

		// delete object
		$query = 'DELETE FROM '.$this->name.
				 ' WHERE '.$key.' = '.$this->database->getEscaped($object->$key);

		return $this->_query($query);
	}

	/*
		Function: unsetObject
			Unsets the object with key from internal object storage.

		Parameters:
			$key - object key

		Returns:
			void
	*/
	public function unsetObject($key) {
		if (isset($this->_objects[$key])) {
			unset($this->_objects[$key]);
		}
	}

	protected function _select(array $options) {

		// select
		$query[] = sprintf('SELECT %s', isset($options['select']) ? $options['select'] : '*');

		// from
		$query[] = sprintf('FROM %s', isset($options['from']) ? $options['from'] : $this->name);

		// where
		if (isset($options['conditions'])) {
			$condition  = '';
			$conditions = (array) $options['conditions'];

			// parse condition
			$parts = explode('?', array_shift($conditions));
			foreach ($parts as $part) {
				$condition .= $part.$this->database->getEscaped(array_shift($conditions));
			}

			if (!empty($condition)) {
				$query[] = sprintf('WHERE %s', $condition);
			}
		}

		// group by
		if (isset($options['group'])) {
			$query[] = sprintf('GROUP BY %s', $options['group']);
		}

		// order
		if (isset($options['order'])) {
			$query[] = sprintf('ORDER BY %s', $options['order']);
		}

		// offset & limit
		if (isset($options['offset']) || isset($options['limit'])) {
			$offset  = isset($options['offset']) ? (int) $options['offset'] : 0;
			$limit   = isset($options['limit']) ? (int) $options['limit'] : 0;
			$query[] = sprintf('LIMIT %s, %s', $offset, $limit);
		}

		return implode(' ', $query);
	}

	protected function _query($query) {
		return $this->database->query($query);
	}

	protected function _queryResult($query) {
		return $this->database->queryResult($query);
	}

	protected function _queryObject($query) {

		// query database
		$result = $this->database->query($query);

		// fetch object and execute init callback
		$object = null;
		if ($object = $this->database->fetchObject($result, $this->class)) {
			$object = $this->_initObject($object);
		}

		$this->database->freeResult($result);
		return $object;
	}

	protected function _queryObjectList($query) {

		// query database
		$result = $this->database->query($query);

		// fetch objects and execute init callback
		$objects = array();
		while ($object = $this->database->fetchObject($result, $this->class)) {
			$objects[$object->{$this->key}] = $this->_initObject($object);
		}

		$this->database->freeResult($result);
		return $objects;
	}

	protected function _initObject($object) {

		// add reference to related app instance
		if (property_exists($object, 'app')) {
			$object->app = $this->app;
		}

		return $object;
	}

}

/*
	Class: AppTableException
*/
class AppTableException extends AppException {}