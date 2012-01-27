<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.application');
jimport('joomla.application.site');

/*
   Class: RouteHelper
   The Helper Class for building links
*/
class RouteHelper extends AppHelper {

	protected $_item_links, $_routed_item_links, $_category_links, $_routed_category_links = array();
	protected $_menu_items;

	public function getLinkBase() {
		return 'index.php?option=' . $this->app->component->self->name;
	}

	public function alphaindex($application_id, $alpha_char = null) {

		// build frontpage link
		$link = $this->getLinkBase() . '&task=alphaindex&app_id='.$application_id;
		$link .= $alpha_char !== null ? '&alpha_char=' . $alpha_char : '';

		if (($menu_item = $this->_findFrontpage($application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function category($category, $route = true) {

		$this->app->table->application->get($category->application_id)->getCategoryTree(true);

		// have we found the link before?
		if ($route) {
			if (isset($this->_routed_category_links[$category->id])) {
				return $this->_routed_category_links[$category->id];
			}
		} else {
			if (isset($this->_category_links[$category->id])) {
				return $this->_category_links[$category->id];
			}
		}

		// build category link
		$link = $this->getLinkBase() . '&task=category&category_id='.$category->id;

		// Priority 1: find direct link to category || Priority 2: find in category path
		if (($menu_item = $this->_findCategory($category->id)) || ($menu_item = $this->_findInCategoryPath($category))) {
			$link .= '&Itemid='.$menu_item->id;
		} else {

			// Priority 3: link to frontpage || Priority 4: current item id
			if (($menu_item = $this->_findFrontpage($category->application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
				$link .= '&Itemid='.$menu_item->id;
			}
		}

		// store link for future lookups
		if ($route) {
			$this->_routed_category_links[$category->id] = JRoute::_($link);
			return $this->_routed_category_links[$category->id];
		} else {
			$this->_category_links[$category->id] = $link;
			return $this->_category_links[$category->id];
		}

		return $link;

	}

	public function comment($comment) {
		return $this->item($comment->getItem()) . '#comment-'.$comment->id;
	}

	public function feed($category, $feed_type) {

		// build feed link
		$link = $this->getLinkBase() . '&task=feed&app_id='.$category->application_id.'&category_id='.$category->id.'&format=feed&type='.$feed_type;

		if (($menu_item = $this->_findFrontpage($category->application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function frontpage($application_id) {

		// build frontpage link
		$link = $this->getLinkBase() . '&task=frontpage';

		if (($menu_item = $this->_findFrontpage($application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function item($item, $route = true) {

		// have we found the link before?
		if ($route) {
			if (isset($this->_routed_item_links[$item->id])) {
				return $this->_routed_item_links[$item->id];
			}
		} else {
			if (isset($this->_item_links[$item->id])) {
				return $this->_item_links[$item->id];
			}
		}

		$this->app->table->application->get($item->application_id)->getCategoryTree(true);

		// build item link
		$link = $this->getLinkBase() . '&task=item&item_id='.$item->id;

		// Priority 1: direct link to item
		$itemid = null;
		if ($menu_item = $this->_findItem($item->id)) {
			$itemid = $menu_item->id;
		}

		// are we in category view?
		$categories = null;
		$category_id = null;
		if (!$itemid && ($this->app->request->getCmd('task') == 'category' || $this->app->request->getCmd('view') == 'category')) {
			// init vars
			$categories = array_filter($item->getRelatedCategoryIds(true));
			$category_id = (int) $this->app->request->getInt('category_id', $this->app->system->application->getParams()->get('category'));
			$category_id = in_array($category_id, $categories) ? $category_id : null;
		}

		if (!$itemid && !$category_id) {
			$primary = $item->getPrimaryCategory();

			// Priority 2: direct link to primary category
			if ($primary && $menu_item = $this->_findCategory($primary->id)) {
				$itemid = $menu_item->id;
			// Priority 3: find in primary category path
			} else if ($primary && $menu_item = $this->_findInCategoryPath($primary)) {
				$itemid = $menu_item->id;
			} else {
				$categories = is_null($categories) ? array_filter($item->getRelatedCategoryIds(true)) : $categories;
				$found = false;
				foreach ($categories as $category) {
					// Priority 4: direct link to any related category
					if ($menu_item = $this->_findCategory($category)) {
						$itemid = $menu_item->id;
						$found = true;
						break;
					}
				}

				if (!$found) {
					$categories = $item->getRelatedCategories(true);
					foreach ($categories as $category) {
						// Priority 5: find in any related categorys path
						if ($menu_item = $this->_findInCategoryPath($category)) {
							$itemid = $menu_item->id;
							$found = true;
							break;
						}
					}
				}

				// Priority 6: link to frontpage
				if (!$found && $menu_item = $this->_findFrontpage($item->application_id)) {
					$itemid = $menu_item->id;
				}
			}
		}

		if ($category_id) {
			$link .= '&category_id=' . $category_id;
		}

		if($itemid) {
			$link .= '&Itemid='.$itemid;
		// Priority 7: current item id
		} else if ($menu = $this->app->object->create('JSite')->getMenu()->getActive()) {
			$link .= '&Itemid='.$menu->id;
		}

		// store link for future lookups
		if ($route) {
			$this->_routed_item_links[$item->id] = JRoute::_($link);
			return $this->_routed_item_links[$item->id];
		} else {
			$this->_item_links[$item->id] = $link;
			return $this->_item_links[$item->id];
		}

	}

	public function mysubmissions($submission) {

		$link = $this->getLinkBase() . '&view=submission&layout=mysubmissions&submission_id='.$submission->id;

		if ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function submission($submission, $type_id, $hash, $item_id = null, $redirect = null) {

		$redirect = !empty($redirect) ? '&redirect='.$redirect : '';
		$item_id  = !empty($item_id) ? '&item_id='.$item_id : '';

		$link = $this->getLinkBase() . '&view=submission&layout=submission&submission_id='.$submission->id.'&type_id='.$type_id.$item_id.'&submission_hash='.$hash.$redirect;

		if ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function tag($application_id, $tag) {

		// build tag link
		$link = $this->getLinkBase() . '&task=tag&tag='.$tag.'&app_id='.$application_id;

		// Priority 1: link to frontpage || Priority 2: current item id
		if (($menu_item = $this->_findFrontpage($application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	protected function _findItem($item_id) {
		$this->_setMenuItems();

		if (isset($this->_menu_items['item'][$item_id])) {
			return $this->_menu_items['item'][$item_id];
		}
	}

	protected function _findCategory($category_id)	{
		$this->_setMenuItems();

		if (isset($this->_menu_items['category'][$category_id])) {
			return $this->_menu_items['category'][$category_id];
		}
	}

	protected function _findInCategoryPath($category) {
		$this->_setMenuItems();

		foreach ($category->getPathway() as $id => $cat) {
			if ($menu_item = $this->_findCategory($id)) {
				return $menu_item;
			}
		}
	}

	protected function _findFrontpage($application_id)	{
		$test = $this->_setMenuItems();

		if (isset($this->_menu_items['frontpage'][$application_id])) {
			return $this->_menu_items['frontpage'][$application_id];
		}
	}

	protected function _setMenuItems() {
		if ($this->_menu_items == null) {
			$component = JComponentHelper::getComponent('com_zoo');

			$menus		= $this->app->object->create('JSite')->getMenu();
			$component_id = $this->app->joomla->isVersion('1.5') ? 'componentid' : 'component_id';
			$menu_items	= $menus->getItems($component_id, $component->id);
			$menu_items = $menu_items ? $menu_items : array();

			$this->_menu_items = array('frontpage' => array(), 'category' => array(), 'item' => array());
			foreach($menu_items as $menu_item) {
				switch (@$menu_item->query['view']) {
					case 'frontpage':
						$this->_menu_items['frontpage'][$this->app->parameter->create($menu_item->params)->get('application')] = $menu_item;
						break;
					case 'category':
						$this->_menu_items['category'][$this->app->parameter->create($menu_item->params)->get('category')] = $menu_item;
						break;
					case 'item':
						$this->_menu_items['item'][$this->app->parameter->create($menu_item->params)->get('item_id')] = $menu_item;
						break;
				}
			}
		}
		return $this->_menu_items;
	}

}
