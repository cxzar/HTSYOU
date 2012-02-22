<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: CategoryHelper
   The Helper Class for category
*/
class CategoryHelper extends AppHelper {

	/*
		Function: getItemsRelatedCategoryIds
			Method to retrieve item's related category id's.

		Returns:
			Array - category id's
	*/
	public function getItemsRelatedCategoryIds($item_id, $published = false) {
		// select item to category relations
		$query = 'SELECT b.id'
		        .' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
		        .' JOIN '.ZOO_TABLE_CATEGORY.' AS b ON a.category_id = b.id'
			    .' WHERE a.item_id='.(int) $item_id
			    .($published == true ? ' AND b.published = 1' : '')
				.' UNION SELECT 0'
				.' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
				.' WHERE a.item_id='.(int) $item_id .' AND a.category_id = 0';

		return $this->app->database->queryResultArray($query);
	}

	/*
		Function: saveCategoryItemRelations
			Method to add category related item's.

		Returns:
			Boolean - true on succes
	*/
	public function saveCategoryItemRelations($item_id, $categories){

		//init vars
		$db = $this->app->database;

		if (!is_array($categories)) {
			$categories = array($categories);
		}

		$categories = array_unique($categories);

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
			    ." WHERE item_id=".(int) $item_id;

		// execute database query
		$db->query($query);

		$query_string = '(%s,' . (int) $item_id.')';
		$category_strings = array();
		foreach ($categories as $category) {
			if (is_numeric($category)) {
				$category_strings[] = sprintf($query_string, $category);
			}
		}

		// add category to item relations
		// insert relation to database
		if (!empty($category_strings)) {
			$query = "INSERT INTO ".ZOO_TABLE_CATEGORY_ITEM
					." (category_id, item_id) VALUES " . implode(',', $category_strings);

			// execute database query
			$db->query($query);
		}

		return true;
	}

	/*
		Function: deleteCategoryItemRelations
			Method to delete category related item's.

		Returns:
			Boolean - true on succes
	*/
	public function deleteCategoryItemRelations($category_id){

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
			    ." WHERE category_id = ".(int) $category_id;

		// execute database query
		return $this->app->database->query($query);

	}

}