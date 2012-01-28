<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class AppExporterZoo2 extends AppExporter {

	protected $_application;
	protected $_categories;

	public function __construct() {
		parent::__construct();
		$this->_name = 'Zoo v2';
	}

	public function export() {

		if (!$this->_application = $this->app->zoo->getApplication()) {
			throw new AppExporterException('No application selected.');
		}

		// export frontpage
		$frontpage = $this->app->object->create('Category');
		$frontpage->name  = 'Root';
		$frontpage->alias = '_root';
		$frontpage->description = $this->_application->description;

		// export categories
		$this->_categories = $this->_application->getCategories();
		$this->_categories[0] = $frontpage;
		foreach ($this->_categories as $category) {
			$this->_addCategory($category);
		}

		// export items
		$types = $this->_application->getTypes();
		$item_table = $this->app->table->item;
		foreach ($types as $type) {
			$items = $item_table->getByType($type->id, $this->_application->id);
			foreach ($items as $key => $item) {
				$this->_addItem($item, $type->name);
			}
		}

		return parent::export();

	}

	protected function _addCategory(Category $category) {

		// store category attributes
		$data = array();
		foreach ($this->category_attributes as $attribute) {
			if (isset($category->$attribute)) {
				$data[$attribute] = $category->$attribute;
			}
		}

		// store category parent
		if (isset($this->_categories[$category->parent])) {
			$data['parent'] = $this->_categories[$category->parent]->alias;
		}

		// store category content params
		$data['content'] = $category->alias == '_root' ? $this->_application->getParams()->get('content.') : $category->getParams()->get('content.');

		parent::_addCategory($category->name, $category->alias, $data);
	}

	protected function _addItem(Item $item, $group = 'default') {

		$data = array();
		foreach ($this->item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$data[$attribute] = $item->$attribute;
			}
		}
		if ($user = $this->app->user->get($item->created_by)) {
			$data['author'] = $user->username;
		}

		$data['tags']	= $item->getTags();

		// store item content, metadata, config params
		$data['content'] = $item->getParams()->get('content.');
		$data['metadata'] = $item->getParams()->get('metadata.');
		$data['config'] = $item->getParams()->get('config.');

		// add categories
		foreach ($item->getRelatedCategoryIds() as $category_id) {
			$alias = '';
			if (empty($category_id)) {
				$alias = '_root';
			} else if (isset($this->_categories[$category_id])) {
				$alias = $this->_categories[$category_id]->alias;
			}
			if (!empty($alias)) {
				$data['categories'][] = $alias;
			}
			if ($item->getPrimaryCategoryId() == $category_id) {
				$data['config']['primary_category'] = $alias;
			}
		}

		foreach ($item->getElements() as $element) {

			switch ($element->getElementType()) {
				case 'relateditems':
					$items = array();
					if ($element->get('item')) {
						foreach ($element->get('item') as $item_id) {
							$items[] = $this->app->alias->item->translateIDToAlias($item_id);
						}
					}
					$element->set('item', $items);

					break;

				case 'relatedcategories':
					$categories = array();
					if ($element->get('category')) {
						foreach ($element->get('category') as $category_id) {
							$categories[] = $this->app->alias->category->translateIDToAlias($category_id);
						}
					}
					$element->set('category', $categories);

					break;

				default:
					break;
			}

			$data['elements'][$element->identifier]['type'] = $element->getElementType();
			$data['elements'][$element->identifier]['name'] = $element->config->get('name');
			$data['elements'][$element->identifier]['data'] = $element->data();

			foreach ($this->app->table->comment->getCommentsForItem($item->id) as $comment) {
				foreach ($this->comment_attributes as $attribute) {
					if (isset($comment->$attribute)) {
						$data['comments'][$comment->id][$attribute] = $comment->$attribute;
					}
				}
				if ($comment->user_type == 'joomla' && ($user = $this->app->user->get($comment->user_id))) {
					$data['comments'][$comment->id]['username'] = $user->username;
				}
			}

		}

		parent::_addItem($item->name, $item->alias, $group, $data);
	}

}