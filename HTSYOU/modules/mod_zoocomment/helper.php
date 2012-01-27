<?php
/**
* @package   ZOO Comment
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CommentModuleHelper
		The comment module helper class
*/
class CommentModuleHelper extends AppHelper {

	const MAX_CHARACTERS = 140;

	public function getLatestComments($application, $categories, $limit) {

		// build where condition
		$where   = array('b.application_id = '.(int) $application->id);
		$where[] = 'a.state = ' . 1;
		$where[] = is_array($categories) ? "c.category_id IN (".implode(",", $categories).")" : "c.category_id = " . $categories;

		// build query options
		$options = array(
			'select'     => 'a.*, b.application_id',
			'from'       => ZOO_TABLE_COMMENT.' AS a'
							. ' LEFT JOIN '.ZOO_TABLE_ITEM.' AS b ON a.item_id = b.id'
							. ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS c ON b.id = c.item_id',
			'conditions' => array(implode(' AND ', $where)),
			'order'      => 'created DESC',
			'group'		 => 'a.id',
			'offset' 	 => 0,
			'limit'		 => $limit);

		// query comment table
		return $this->app->table->comment->all($options);
	}

}