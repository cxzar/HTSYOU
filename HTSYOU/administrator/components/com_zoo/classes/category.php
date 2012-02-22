<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: Category
		Category related attributes and functions.
*/
class Category {

    /*
       Variable: id
         Primary key.
    */
	public $id;

    /*
       Variable: application_id
         Related application id.
    */
	public $application_id;

    /*
       Variable: name
         Category name.
    */
	public $name;

    /*
		Variable: alias
			Category alias.
    */
	public $alias;

    /*
       Variable: description
         Category description.
    */
	public $description;

    /*
       Variable: parent
         Categories parent id.
    */
	public $parent;

    /*
       Variable: ordering
         Categories ordering.
    */
	public $ordering;

    /*
       Variable: published
         Category published state.
    */
	public $published;

    /*
       Variable: params
         Category params.
    */
	public $params;

    /*
       Variable: item_ids
         Related category item ids.
    */
	public $item_ids;

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
       Variable: _parent
         Related category parent object.
    */
	protected $_parent;

    /*
       Variable: _children
         Related category children objects.
    */
	protected $_children = array();

    /*
       Variable: _items
         Related category item objects.
    */
	protected $_items = array();

    /*
       Variable: _item_count
         Related category item count.
    */
	protected $_item_count;

	/*
       Variable: _total_item_count
         Item count including subcategories.
    */
	protected $_total_item_count = null;

	public function  __construct() {

		// init vars
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);

		// set related item ids
		$this->item_ids = isset($this->item_ids) ? explode(',', $this->item_ids) : array();
		if (!empty($this->item_ids)) {
			$this->item_ids = array_combine($this->item_ids, $this->item_ids);
		}
	}

	/*
		Function: getApplication
			Get related application object.

		Returns:
			Application - application object
	*/
	public function getApplication() {
		return $this->app->table->application->get($this->application_id);
	}

	/*
		Function: hasChildren
			Does this category have children.

		Returns:
			Bool
	*/
	public function hasChildren() {
		return !empty($this->_children);
	}

	/*
		Function: getChildren
			Method to get category's children.

		Parameters:
	      recursive - Recursivly retrieve childrens children.

		Returns:
			id - children
	*/
	public function getChildren($recursive = false) {

		if ($recursive) {
			$children = array();

			foreach ($this->_children as $child) {
				$children[$child->id] = $child;
				$children 			 += $child->getChildren(true);
			}

			return $children;
		}

		return $this->_children;
	}

	/*
    	Function: setChildren
    	  Set children.

	   Returns:
	      Category
 	*/
	public function setChildren($val) {
		$this->_children = $val;
		return $this;
	}

	/*
    	Function: addChild
    	  Add child.

	   Returns:
	      Category
 	*/
	public function addChild($child) {
		$this->_children[$child->id] = $child;
		return $this;
	}

	/*
    	Function: removeChild
    	  Remove a child.

	   Returns:
	      Category
 	*/
	public function removeChild($child) {
		unset($this->_children[$child->id]);
		return $this;
	}

	/*
		Function: getParent
			Method to get category's parent.

		Returns:
			id - parent
	*/
	public function getParent() {
		return $this->_parent;
	}

	/*
    	Function: setParent
    	  Set parent.

	   Returns:
	      Category
 	*/
	public function setParent($val) {
		$this->_parent = $val;
		return $this;
	}

	/*
		Function: getPathway
			Method to get category's pathway.

		Returns:
			Array - Array of parent categories
	*/
	public function getPathway() {
		if ($this->_parent == null) {
			return array();
		}

		$pathway   = $this->_parent->getPathway();
		$pathway[$this->id] = $this;

		return $pathway;
	}

	/*
    	Function: isPublished
    	  Get published state.

	   Returns:
	      -
 	*/
	public function isPublished() {
		return $this->published;
	}

	/*
		Function: setPublished
			Set category published state and fire event.

		Parameters:
			$val - State
			$save - Autosave category before fire event

		Returns:
			Category
	*/
	public function setPublished($val, $save = false) {

		if ($this->published != $val) {

			// set state
			$old_state   = $this->state;
			$this->published = $val;

			// autosave category ?
			if ($save) {
				$this->app->table->category->save($this);
			}

			// fire event
		    $this->app->event->dispatcher->notify($this->app->event->create($this, 'category:stateChanged', compact('old_state')));
		}

		return $this;

	}

	/*
		Function: getPath
			Method to get the path to this category.

		Returns:
			Array - Category path
	*/
	public function getPath($path = array()) {

		$path[] = $this->id;

		if ($this->_parent != null) {
			$path = $this->_parent->getPath($path);
		}

		return $path;
	}

	/*
		Function: getItems
			Method to get category's items.

		Returns:
			Array
	*/
	public function getItems($published = false, $user = null, $orderby = '') {
		if (empty($this->_items)) {
			$this->_items = $this->app->table->item->getByCategory($this->application_id, $this->id, $published, $user, $orderby);
		}

		return $this->_items;
	}

	/*
		Function: itemCount
			Method to count category's items.

		Returns:
			Int - Number of items
	*/
	public function itemCount() {
		if (!isset($this->_item_count)) {
			$this->_item_count = count($this->item_ids);
		}
		return $this->_item_count;
	}

	/*
		Function: total_item_count
			Method to count category's published items including all childrens items.

		Returns:
			Int - Number of items
	*/
	public function totalItemCount() {
		if (!isset($this->_total_item_count)) {
			$this->_total_item_count = count($this->getItemIds(true));
		}

		return $this->_total_item_count;
	}

	/* @deprecated use countItems instead */
	public function countItems() {
		return $this->totalItemCount();
	}

	/* @deprecated use childrenHaveItems instead */
	public function countChildrensItems() {
		return $this->childrenHaveItems();
	}

	/*
		Function: getItemIds
			Method to get related item ids.

		Returns:
			Array - Related item ids
	*/
	public function getItemIds($recursive = false) {
		$item_ids = $this->item_ids;
		if ($recursive) {
			foreach($this->getChildren(true) as $child) {
				$item_ids += $child->item_ids;
			}
		}

		return $item_ids;
	}

	/*
		Function: childrenHaveItems
			Method to check if children have items.

		Returns:
			Bool
	*/
	public function childrenHaveItems() {
		foreach ($this->getChildren(true) as $child) {
			if ($child->itemCount()) {
				return true;
			}
		}

		return false;
	}

	/*
		Function: getParams
			Gets category params.

		Parameters:
  			$for - Get params for a specific use, including overidden values.

		Returns:
			ParameterData - params
	*/
	public function getParams($for = null) {

		// get site params and inherit globals
		if ($for == 'site') {

			return $this->app->parameter->create()
				->set('config.', $this->getApplication()->getParams()->get('global.config.'))
				->set('template.', $this->getApplication()->getParams()->get('global.template.'))
				->loadArray($this->params);
		}

		return $this->params;
	}

	/*
		Function: getImage
		  Get image resource info.

		Parameters:
	      $name - the param name of the image

	   Returns:
	      Array - Image info
	*/
	public function getImage($name) {
		if ($image = $this->params->get($name)) {
			return $this->app->html->_('zoo.image', $image, $this->params->get($name . '_width'), $this->params->get($name . '_height'));
		}
		return null;
	}

	/*
		Function: getImage
		  Executes Content Plugins on text.

		Parameters:
	      $text - the text

	   Returns:
	      text - string
	*/
	public function getText($text) {
		return $this->app->zoo->triggerContentPlugins($text, array(), 'com_zoo.category.description');
	}

}

/*
	Class: CategoryException
*/
class CategoryException extends AppException {}