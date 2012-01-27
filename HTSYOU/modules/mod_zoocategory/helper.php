<?php
/**
* @package   ZOO Category
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CategoryModuleHelper
		The category module helper class
*/
class CategoryModuleHelper extends AppHelper {

    public function render($category, $params, $level, $flat = false) {

		// init vars
		$max_depth = $params->get('depth', 0);

		if ($menu_item = $params->get('menu_item')) {
			$url = $this->app->link(array('task' => 'category', 'category_id' => $category->id, 'Itemid' => $menu_item));
		} else {
			$url = JRoute::_($this->app->route->category($category));
		}

		$result   = array();
		$result[] = '<li>';
		$result[] = '<a href="'.$url.'">'.$category->name.'</a>';
		if ($flat) $result[] = '</li>';
		if ((!$max_depth || $max_depth >= $level) && ($children = $category->getChildren()) && !empty($children)) {

			if (!$flat) $result[] = '<ul class="level'.$level.'">';
			foreach ($children as $child) {
				$result[] = $this->render($child, $params, $level+1, $flat);
			}

			if (!$flat) $result[] = '</ul>';
		}

		if (!$flat) $result[] = '</li>';

		return implode("\n", $result);
	}

}