<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: Item
		Item related attributes and functions.
*/
class Item {

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
       Variable: type
         Item type.
    */
	public $type;

    /*
       Variable: name
         Item name.
    */
	public $name;

    /*
		Variable: alias
			Item alias.
    */
	public $alias;

    /*
       Variable: created
         Creation date.
    */
	public $created;

    /*
       Variable: modified
         Modified date.
    */
	public $modified;

    /*
       Variable: modified_by
         Modified by user.
    */
	public $modified_by;

    /*
       Variable: publish_up
         Publish up date.
    */
	public $publish_up;

    /*
       Variable: publish_down
         Publish down date.
    */
	public $publish_down;

    /*
       Variable: priority
         Item priority.
    */
	public $priority = 0;

    /*
       Variable: hits
         Hit count.
    */
	public $hits = 0;

    /*
       Variable: state
         Item published state.
    */
	public $state = 0;

    /*
       Variable: searchable
         Item searchable.
    */
	public $searchable = 1;

    /*
       Variable: access
         Item access level.
    */
	public $access;

    /*
       Variable: created_by
         Item created by user.
    */
	public $created_by;

    /*
       Variable: created_by_alias
         Item created by alias.
    */
	public $created_by_alias;

    /*
       Variable: params
         Item params.
    */
	public $params;

    /*
       Variable: elements
         Item elements.
    */
	public $elements;

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
       Variable: _type
         Related type object.
    */
	protected $_type;

    /*
       Variable: elements
         Element objects.
    */
	protected $_elements;

    /*
       Variable: tags
         Tag objects.
    */
	protected $_tags;

    /*
       Variable: _primary_category
         Primary Category Object.
    */
	protected $_primary_category;

    /*
       Variable: _related_categories
         Items related categories.
    */
	protected $_related_categories;

    /*
       Variable: _related_categories_ids
         Items related category ids.
    */
	protected $_related_category_ids;

 	/*
		Function: Constructor

		Returns:
			Order
	*/
	public function __construct() {

		// get app instance
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);

		// decorate data as object
		$this->elements = $app->data->create($this->elements);

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
		Function: getType
			Get related type object.

		Returns:
			Type - type object
	*/
	public function getType() {

		if (empty($this->_type)) {
			$this->_type = $this->getApplication()->getType($this->type);
		}

		return $this->_type;
	}

	/*
		Function: getAuthor
			Get author name.

		Returns:
			String - author name
	*/
	public function getAuthor() {

		$author = $this->created_by_alias;

		if (!$author) {

			$user = $this->app->user->get($this->created_by);

			if ($user && $user->id) {
				$author = $user->name;
			}
		}

		return $author;
	}

	/*
    	Function: getState
    	  Get published state.

	   Returns:
	      -
 	*/
	public function getState() {
		return $this->state;
	}

	/*
		Function: setState
			Set published state and fire event.

		Parameters:
			$state - State
			$save - Autosave comment before fire event

		Returns:
			Comment
	*/
	public function setState($state, $save = false) {

		if ($this->state != $state) {

			// set state
			$old_state   = $this->state;
			$this->state = $state;

			// autosave comment ?
			if ($save) {
				$this->app->table->item->save($this);
			}

			// fire event
		    $this->app->event->dispatcher->notify($this->app->event->create($this, 'item:stateChanged', compact('old_state')));
		}

		return $this;
	}

	/*
    	Function: getSearchable
    	  Get published searchable.

	   Returns:
	      -
 	*/
	public function getSearchable() {
		return $this->searchable;
	}

	/*
    	Function: setSearchable
    	  Set published searchable.

	   Returns:
	      -
 	*/
	public function setSearchable($val) {
		$this->searchable = $val;
	}

	/*
    	Function: getElement
    	  Get element object by identifier.

	   	  Returns:
	        Object
 	*/
	public function getElement($identifier) {

		if (isset($this->_elements[$identifier])) {
			return $this->_elements[$identifier];
		}

		if ($element = $this->getType()->getElement($identifier)) {
			$element->setItem($this);
			$this->_elements[$identifier] = $element;
			return $element;
		}

		return null;
	}

	/*
    	Function: getCoreElements
    	  Get core elements.

	   	  Returns:
	        Array - element objects
 	*/
	public function getCoreElements() {

		// get types core elements
		if ($type = $this->getType()) {
			$core_elements = $type->getCoreElements();
			foreach ($core_elements as $element) {
				$element->setItem($this);
			}
			return $core_elements;
		}

		return array();
	}

	/*
    	Function: getElements
    	  Get all element objects from the parsed type elements xml.

	   	  Returns:
	        Array - Array of element objects
 	*/
	public function getElements() {

		// get types elements
		if ($type = $this->getType()) {
			foreach ($type->getElements() as $element) {
				if (!isset($this->_elements[$element->identifier])) {
					$element->setItem($this);
					$this->_elements[$element->identifier] = $element;
				}
			}
			$this->_elements = $this->_elements ? $this->_elements : array();
		}

		return $this->_elements;
	}

	/*
    	Function: getSubmittableElements
    	  Get all submittable element objects for this item.

	   	  Returns:
	        Array - Array of submittable element objects
 	*/
	public function getSubmittableElements() {
		return	array_filter($this->getElements(), create_function('$element', 'return $element instanceof iSubmittable;'));
	}

	/*
		Function: getRelatedCategories
			Method to retrieve item's related categories.

		Parameters:
			$published - Only published categories

		Returns:
			Array - category id's
	*/
	public function getRelatedCategories($published = false) {
		if ($this->_related_categories === null) {
			$this->_related_categories = $this->app->table->category->getById($this->getRelatedCategoryIds($published), $published);
		}
		return $this->_related_categories;
	}

	/*
		Function: getRelatedCategoryIds
			Method to retrieve item's related category id's.

		Returns:
			Array - category id's
	*/
	public function getRelatedCategoryIds($published = false) {
		if ($this->_related_category_ids === null) {
			$this->_related_category_ids = $this->app->category->getItemsRelatedCategoryIds($this->id, $published);
		}
		return $this->_related_category_ids;
	}

	/*
		Function: getPrimaryCategory
			Method to retrieve item's primary category.

		Returns:
			Category - category
	*/
	public function getPrimaryCategory() {
		if (empty($this->_primary_category)) {
			$table = $this->app->table->category;
			if ($id = $this->getPrimaryCategoryId()) {
				$this->_primary_category = $table->get($id);
			}
		}

		return $this->_primary_category;
	}

	/*
		Function: getPrimaryCategoryId
			Method to retrieve item's primary category id.

		Returns:
			Category - category
	*/
	public function getPrimaryCategoryId() {
		return (int) $this->getParams()->get('config.primary_category', null);
	}

	/*
		Function: getParams
			Gets item params.

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
		Function: getTags
			Gets item related tags.

		Returns:
			Object - array with tags
	*/
	public function getTags() {

		if ($this->_tags === null) {
			$this->_tags = $this->app->table->tag->getItemTags($this->id);
		}

		return $this->_tags;
	}

	/*
    	Function: setTags
    	  Bind tag array to object.

	   Returns:
	      Void
 	*/
	public function setTags($tags = array()) {

		$this->_tags = array_filter($tags);

		return $this;
	}

	/*
    	Function: canAccess
    	  Check if item is accessible with users access rights.

	   Returns:
	      Boolean - True, if access granted
 	*/
	public function canAccess($user = null) {
		return $this->app->user->canAccess($user, $this->access);
	}

	/*
		Function: hit
			Method to increment the hit counter

		Returns:
			Boolean - true on success
	*/
	public function hit() {
		return $this->app->table->item->hit($this);
	}

	/*
		Function: getComments
			Get items comments.

		Returns:
			Array - comments
	*/
	public function getComments() {
		return $this->app->table->comment->getCommentsForItem($this->id, $this->getApplication()->getParams()->get('global.comments.order', 'ASC'), $this->app->comment->activeAuthor());
	}

	/*
    	Function: getCommentTree
    	  Get comments as tree.

	   Returns:
	      Array - comments
 	*/
	public function getCommentTree() {
		return $this->app->tree->build($this->getComments(), 'Comment', $this->getApplication()->getParams()->get('global.comments.max_depth'), 'parent_id');
	}

	/*
		Function: getCommentsCount
			Get items comment count.

		Parameters:
  			$state - Specifiy the comments state to count

		Returns:
			int - Number of comments
	*/
	public function getCommentsCount($state = 1) {
		return $this->app->table->comment->count(array('conditions' => array('item_id = ? AND state = ?', $this->id, $state)));
	}

	/*
    	Function: isPublished
    	  Checks wether the item is currently published.

	   Returns:
	      Boolean.
 	*/
	public function isPublished() {

		// get dates
		$now  = $this->app->date->create()->toMySQL();
		$null = $this->app->database->getNullDate();

		return $this->state == 1
				&& ($this->publish_up == $null || $this->publish_up <= $now)
				&& ($this->publish_down == $null || $this->publish_down >= $now);
	}

	/*
    	Function: isCommentsEnabled
    	  Checks wether comments are activated, globally and item specific.

	   Returns:
	      Boolean.
 	*/
	public function isCommentsEnabled() {
		return $this->getParams()->get('config.enable_comments', 1);
	}

	/*
    	Function: subscribe
    	  Subscribe email address to item

	   Returns:
	      Item
 	*/
	public function subscribe($mail, $name = '') {

		$subscribers = (array) $this->getParams()->get('comments.subscribers');
		if (!in_array($mail, array_keys($subscribers))) {
			$subscribers[$mail] = $name;
			$this->getParams()->set('comments.subscribers', $subscribers);
		}

		return $this;
	}

	/*
    	Function: unsubscribe
    	  Unsubscribe email address from item

	   Returns:
	      Item
 	*/
	public function unsubscribe($mail) {

		$subscribers = (array) $this->getParams()->get('comments.subscribers');
		if (key_exists($mail, $subscribers)) {
			unset($subscribers[$mail]);
			$this->getParams()->set('comments.subscribers', $subscribers);
		}

		return $this;
	}

	/*
    	Function: getSubscribers
    	  Get subscribers to this item

	   Returns:
	      Array - Subscribers
 	*/
	public function getSubscribers() {
		return (array) $this->getParams()->get('comments.subscribers');
	}

}

/*
	Class: ItemException
*/
class ItemException extends AppException {}