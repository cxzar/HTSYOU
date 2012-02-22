<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DatabaseHelper
		Helper for database
*/
class DatabaseHelper extends AppHelper {

	/* name */
	public $name;

	/* database */
	protected $_database;

	public function __construct($app) {
		parent::__construct($app);

		// set database
		$this->_database = $this->app->system->dbo;
		$this->name      = $this->_database->name;
	}

	public function query($query) {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->query();

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryResult($query) {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadResult();

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryObject($query) {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadObject();

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryObjectList($query, $key = '') {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadObjectList($key);

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryResultArray($query, $numinarray = 0) {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadResultArray($numinarray);

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryAssoc($query) {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadAssoc();

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function queryAssocList($query, $key = '') {

		// query database table
		$this->_database->setQuery($query);
		$result = $this->_database->loadAssocList($key);

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function insertObject($table, $object, $key = null) {

		// insert object
		$result = $this->_database->insertObject($table, $object, $key);

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function updateObject($table, $object, $key, $updatenulls = true) {

		// update object
		$result = $this->_database->updateObject($table, $object, $key, $updatenulls);

		// throw exception, on database error
		if ($this->_database->getErrorNum()) {
			throw new AppDatabaseException(__METHOD__.' failed. ('.$this->_database->getErrorMsg().')');
		}

		return $result;
	}

	public function fetchRow($result) {

		if ($this->name == 'mysqli') {
			return mysqli_fetch_row($result);
		}

		return mysql_fetch_row($result);
	}

	public function fetchArray($result, $type = MYSQL_BOTH) {

		if ($this->name == 'mysqli') {
			return mysqli_fetch_array($result, $type);
		}

		return mysql_fetch_array($result, $type);
	}

	public function fetchObject($result, $class = 'stdClass') {

		if ($this->name == 'mysqli') {
			return $class != 'stdClass' ? mysqli_fetch_object($result, $class) : mysqli_fetch_object($result);
		}

		return $class != 'stdClass' ? mysql_fetch_object($result, $class) : mysql_fetch_object($result);
	}

	public function freeResult($result) {

		if ($this->name == 'mysqli') {
			return mysqli_free_result($result);
		}

		return mysql_free_result($result);
	}

    public function __call($method, $args) {
		return $this->_call(array($this->_database, $method), $args);
    }

	public function replacePrefix($sql, $prefix='#__') {
		return preg_replace('/'.preg_quote($prefix).'/', $this->_database->getPrefix(), $sql);
	}

}

/*
	Class: AppDatabaseException
*/
class AppDatabaseException extends AppException {}