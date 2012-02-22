<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

	protected $_links = array();
	protected $_menu_items;

	public function getLinkBase() {
		return 'index.php?option=' . $this->app->component->self->name;
	}

	public function alphaindex($application_id, $alpha_char = null) {

		// build frontpage link
		$link = $this->getLinkBase() . '&task=alphaindex&app_id='.$application_id;
		$link .= $alpha_char !== null ? '&alpha_char=' . $alpha_char : '';

		if (($menu_item = $this->_find('frontpage', $application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function category($category, $route = true) {

		// have we found the link before?
		if ($route) {
			if (isset($this->_links['category.routed'][$category->id])) {
				return $this->_links['category.routed'][$category->id];
			}
		} else {
			if (isset($this->_links['category'][$category->id])) {
				return $this->_links['category'][$category->id];
			}
		}

		$this->app->table->application->get($category->application_id)->getCategoryTree(true);

		// Priority 1: direct link to category
		if ($menu_item = $this->_find('category', $category->id)) {

			$link = $menu_item->link.'&Itemid='.$menu_item->id;

		} else {

			// build category link
			$link = $this->getLinkBase() . '&task=category&category_id='.$category->id;

			// Priority 2: find in category path
			if ($menu_item = $this->_findInCategoryPath($category)) {
				$link .= '&Itemid='.$menu_item->id;
			} else {
				// Priority 3: link to frontpage || Priority 4: current item id
				if (($menu_item = $this->_find('frontpage', $category->application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
					$link .= '&Itemid='.$menu_item->id;
				}
			}
		}

		// store link for future lookups
		if ($route) {
			return $this->_links['category.routed'][$category->id] = JRoute::_($link);
		} else {
			return $this->_links['category'][$category->id] = $link;
		}

	}

	public function comment($comment, $route = true) {
		return $this->item($comment->getItem(), $route) . '#comment-'.$comment->id;
	}

	public function feed($category, $feed_type) {

		// build feed link
		$link = $this->getLinkBase() . '&task=feed&app_id='.$category->application_id.'&category_id='.$category->id.'&format=feed&type='.$feed_type;

		if (($menu_item = $this->_find('frontpage', $category->application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function frontpage($application_id) {

		// Priority 1: direct link to frontpage
		if ($menu_item = $this->_find('frontpage', $application_id)) {
			return $menu_item->link.'&Itemid='.$menu_item->id;
		}

		// build frontpage link
		$link = $this->getLinkBase() . '&task=frontpage';

		// Priority 2: current item id
		if ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function item($item, $route = true) {

		// have we found the link before?
		if ($route) {
			if (isset($this->_links['item.routed'][$item->id])) {
				return $this->_links['item.routed'][$item->id];
			}
		} else {
			if (isset($this->_links['item'][$item->id])) {
				return $this->_links['item'][$item->id];
			}
		}

		// Priority 1: direct link to item
		if ($menu_item = $this->_find('item', $item->id)) {

			$link = $menu_item->link.'&Itemid='.$menu_item->id;

		} else {

			$itemid = null;

			// build item link
			$link = $this->getLinkBase() . '&task=item&item_id='.$item->id;

			// are we in category view?
			$this->app->table->application->get($item->application_id)->getCategoryTree(true);
			$categories = null;
			$category_id = null;
			if ($this->app->request->getCmd('task') == 'category' || $this->app->request->getCmd('view') == 'category') {
				// init vars
				$categories = array_filter($item->getRelatedCategoryIds(true));
				$category_id = (int) $this->app->request->getInt('category_id', $this->app->system->application->getParams()->get('category'));
				$category_id = in_array($category_id, $categories) ? $category_id : null;
			}

			if (!$category_id) {
				$primary = $item->getPrimaryCategory();

				// Priority 2: direct link to primary category
				if ($primary && $menu_item = $this->_find('category', $primary->id)) {
					$itemid = $menu_item->id;
				// Priority 3: find in primary category path
				} else if ($primary && $menu_item = $this->_findInCategoryPath($primary)) {
					$itemid = $menu_item->id;
				} else {
					$categories = is_null($categories) ? array_filter($item->getRelatedCategoryIds(true)) : $categories;
					$found = false;
					foreach ($categories as $category) {
						// Priority 4: direct link to any related category
						if ($menu_item = $this->_find('category', $category)) {
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
					if (!$found && $menu_item = $this->_find('frontpage', $item->application_id)) {
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
			} else if ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive()) {
				$link .= '&Itemid='.$menu_item->id;
			}
		}

		// store link for future lookups
		if ($route) {
			return $this->_links['item.routed'][$item->id] = JRoute::_($link);
		} else {
			return $this->_links['item'][$item->id] = $link;
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
		if (($menu_item = $this->_find('frontpage', $application_id)) || ($menu_item = $this->app->object->create('JSite')->getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	protected function _findInCategoryPath($category) {
		foreach ($category->getPathway() as $id => $cat) {
			if ($menu_item = $this->_find('category', $id)) {
				return $menu_item;
			}
		}
	}

	protected function _find($type, $id) {
		if ($this->_menu_items == null) {
			$component_id = $this->app->joomla->isVersion('1.5') ? 'componentid' : 'component_id';
			$menu_items	= $this->app->object->create('JSite')->getMenu()->getItems($component_id, JComponentHelper::getComponent('com_zoo')->id);
			$menu_items = $menu_items ? $menu_items : array();

			$this->_menu_items = array_fill_keys(array('frontpage', 'category', 'item', 'submission', 'mysubmissions'), array());
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
					case 'submission':
						$this->_menu_items[(@$menu_item->query['layout'] == 'submission' ? 'submission' : 'mysubmissions')][$this->app->parameter->create($menu_item->params)->get('submission')] = $menu_item;
						break;
				}
			}
		}

		return @$this->_menu_items[$type][$id];
	}

}
