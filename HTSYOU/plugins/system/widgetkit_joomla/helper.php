<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');

class WidgetkitJoomlaWidgetkitHelper extends WidgetkitHelper {

	public function renderItem($item, $params) {

		$result = $item->introtext;

		if ($params->get('readmore') && $item->readmore) {
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid));
			$result .= '<a class="readmore" href="'.$link.'">' . JText::_('COM_CONTENT_READ_MORE_TITLE') . '</a>';
		}

		return $result;

	}

	public function getList($params) {

		if (!$catid = (int) $params->get('catid', 0)) {
			return array();
		}

		// Ordering
		$direction = null;
		switch ($params->get('order')) {
			case 'random':
				$ordering = 'RAND()';
				break;
			case 'date':
				$ordering = 'created';
				break;
			case 'rdate':
				$ordering = 'created';
				$direction = 'DESC';
				break;
			case 'alpha':
				$ordering = 'title';
				break;
			case 'ralpha':
				$ordering = 'title';
				$direction = 'DESC';
				break;
			case 'hits':
				$ordering = 'hits';
				break;
			case 'rhits':
				$ordering = 'hits';
				$direction = 'DESC';
				break;
			case 'ordering':
			default:
				$ordering = 'a.ordering';
				break;
		}

		JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
		$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		$model->setState('params', JFactory::getApplication()->getParams());
		$model->setState('filter.category_id', $catid);
		$model->setState('filter.published', 1);
		$model->setState('filter.access', true);
		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $direction);
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('items', 0));
		$model->setState('filter.subcategories', $params->get('subcategories'));
		$model->setState('filter.max_category_levels', 999);

		return $model->getItems();
	}
}
