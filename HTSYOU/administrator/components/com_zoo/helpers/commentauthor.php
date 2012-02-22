<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: CommentAuthorHelper
   	  Comment author helper class.
*/
class CommentAuthorHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('CommentAuthor', 'classes:commentauthor.php');
	}

	/*
		Function: create
			Creates a Comment author instance

		Parameters:
			$type - Comment author type

		Returns:
			CommentAuthor
	*/
	public function create($type = '', $args = array()) {

		// load renderer class
		$class = $type ? 'CommentAuthor'.$type : 'CommentAuthor';

		return $this->app->object->create($class, $args);

	}

}