<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TagHelper
		The general helper Class for tags
*/
class TagHelper extends AppHelper {

	public function loadtags($application_id, $tag) {

		$tags = array();
		if (!empty($tag)) {
			// get tags
			$tag_objects = $this->app->table->tag->getAll($application_id, $tag, '', 'a.name asc');

			foreach($tag_objects as $tag) {
				$tags[] = $tag->name;
			}
		}

		return json_encode($tags);
	}

}
