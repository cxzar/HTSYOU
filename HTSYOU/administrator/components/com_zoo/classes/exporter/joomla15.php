<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class AppExporterJoomla15 extends AppExporter {

	public function __construct() {
		parent::__construct();
		$this->_name = 'Joomla 1.5';
	}

	public function isEnabled() {
		return $this->app->joomla->isVersion('1.5');
	}

	public function export() {

		$db = $this->app->database;
	    $query = "SELECT * FROM #__sections"
	    		." WHERE scope='content'";
	    $sections = $db->queryObjectList($query);

		// get image path
		$image_path = JComponentHelper::getParams('com_media')->get('image_path');

	    foreach($sections as $section) {
	    	$data = array();
			foreach ($this->category_attributes as $attribute) {
				if (isset($section->$attribute)) {
					$data[$attribute] = $section->$attribute;
				}
			}
			if ($section->image) {
				$data['content']['image'] = $image_path.'/'.$section->image;
			}
			$this->_addCategory($section->title, $section->alias, $data);

			$query = "SELECT * FROM #__categories WHERE section = " . $section->id;
			$joomla_categories = $db->queryObjectList($query);

			foreach($joomla_categories as $joomla_category) {

				$data = array();
				foreach ($this->category_attributes as $attribute) {
					if (isset($joomla_category->$attribute)) {
						$data[$attribute] = $joomla_category->$attribute;
					}
				}
				$data['parent'] = $section->alias;

				if ($joomla_category->image) {
					$data['content']['image'] = $image_path.'/'.$joomla_category->image;
				}
		    	$this->_addCategory($joomla_category->title, $joomla_category->alias, $data);

				$query = "SELECT * FROM #__content WHERE catid =" . $joomla_category->id;
				$articles = $db->queryObjectList($query);

				foreach ($articles as $article) {
					if ($article->state != -2) {
						$this->_addItem($article, $joomla_category->alias, JText::_('Joomla article'));
					}
				}
			}
	    }

		$query = "SELECT * FROM #__content WHERE catid = 0";
		$articles = $db->queryObjectList($query);

		foreach ($articles as $article) {
			if ($article->state != -2) {
				$this->_addItem($article, 0, JText::_('Joomla article'));
			}
		}

		return parent::export();

	}

	protected function _addItem($article, $parent, $group = 'default') {
		$data = array();
		foreach ($this->item_attributes as $attribute) {
			if (isset($article->$attribute)) {
				$data[$attribute] = $article->$attribute;
			}
		}
		if (!$this->app->user->get($article->created_by)) {
			return;
		}

		$metadata = $this->app->parameter->create($article->metadata);
		$data['metadata'] = array('description' => $article->metadesc, 'keywords' => $article->metakey, 'robots' => $metadata->get('robots'), 'author' => $metadata->get('author'));

		$data['author'] = $this->app->user->get($article->created_by)->username;

		$data['categories'][] = $parent;

		$data['elements'][0]['type'] = 'textarea';
		$data['elements'][0]['name'] = 'Article';
		$data['elements'][0]['data'] = array(array('value' => $article->introtext), array('value' => $article->fulltext));

		parent::_addItem($article->title, $article->alias, $group, $data);
	}

}