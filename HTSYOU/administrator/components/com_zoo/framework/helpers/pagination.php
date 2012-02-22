<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: PaginationHelper
		Pagination helper class. Wrapper for JPagination.
*/
class PaginationHelper extends AppHelper {  

	/*
		Function: create
			Create a pagination object

		Parameters:
			$total - Items total
			$limitstart - Offset to start at
			$limit - Items per page
			$name - Pagination name
			$type - Pagination type

		Returns:
			Mixed
	*/
	public function create($total, $limitstart, $limit, $name = '', $type = '') {

		if (empty($type)) {

			// load class
			jimport('joomla.html.pagination');

			return new JPagination($total, $limitstart, $limit);

		}

		// load Pagination class
		$class = $type.'Pagination';
		$this->app->loader->register($class, 'classes:pagination.php');
		
		return $this->app->object->create($class, array($name, $total, $limitstart, $limit));
		
	}

}