<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: AliasHelper
   The Helper Class for aliases
*/
class AliasHelper extends AppHelper {

	public $table;

	/*
		Function: __get
			Retrieve this helper with table set

		Parameters:
			$name - Table name

		Returns:
			this
	*/
	public function __get($name) {
		if (in_array($name, array('application', 'category', 'item', 'submission'))) {
			$this->table = $this->app->table->$name;
		}
		return $this;
	}

	/*
		Function: translateIDToAlias
			Translate object id to alias.

		Parameters:
			$id - Object id

		Returns:
			Mixed - Null or Object alias string
	*/
	public function translateIDToAlias($id){
		if ($object = $this->table->get($id)) {
			return $object->alias;
		}

		return null;
	}

	/*
		Function: translateAliasToID
			Translate object alias to id.

		Return:
			Int - The object id or 0 if not found
	*/
	public function translateAliasToID($alias) {

		// init vars
		$db = $this->app->database;

		// search alias
		$query = 'SELECT id'
			    .' FROM '.$this->table->name
			    .' WHERE alias = '.$db->Quote($alias)
				.' LIMIT 1';

		return $db->queryResult($query);
	}

	/*
		Function: getAlias
			Get unique object alias.

		Parameters:
			$id - Object id
			$alias - Object alias

		Returns:
			Mixed - Null or Object alias string
	*/
	public function getUniqueAlias($id, $alias = '') {

		if (empty($alias) && $id) {
			$alias = JFilterOutput::stringURLSafe($this->table->get($id)->name);
		}

		if (!empty($alias)) {
			$i = 2;
			$new_alias = $alias;
			while ($this->checkAliasExists($new_alias, $id)) {
				$new_alias = $alias . '-' . $i++;
			}
			return $new_alias;
		}

		return $alias;
	}

	/*
 		Function: checkAliasExists
 			Method to check if a alias already exists.
	*/
	public function checkAliasExists($alias, $id = 0) {

		$xid = intval($this->translateAliasToID($alias));
		if ($xid && $xid != intval($id)) {
			return true;
		}

		return false;
	}

}