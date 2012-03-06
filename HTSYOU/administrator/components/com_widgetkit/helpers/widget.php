<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
/**
* @package   Widgetkit Component
* @file      widget.php
* @version   1.0.0 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: WidgetWidgetkitHelper
		Widget helper class. Create and manage Widgets.
*/
class WidgetWidgetkitHelper extends WidgetkitHelper {

	/* database */
	public $db;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// init vars
		$this->db = JFactory::getDBO();
	}
	
	/*
		Function: get
			Retrieve a widget instance by id.

		Returns:
			Array
	*/	
	public function get($id) {

		// set query
		$this->db->setQuery('SELECT * FROM #__widgetkit_widget WHERE id='.$id);

		// get widget
		$widget = $this->db->loadObject();

		return is_object($widget) ? new WidgetkitWidget($id, $widget->type, $widget->style, $widget->name, $widget->content, $widget->created, $widget->modified) : null;
	}

	/*
		Function: all
			Retrieve all widget instances.

		Returns:
			Array
	*/	
	public function all($type = null) {
		
		// init vars
		$widgets = array();

		// set query
		$this->db->setQuery('SELECT * FROM #__widgetkit_widget '.($type ? ' WHERE type="'.$type.'"' : null).' ORDER BY name ASC');

		foreach ((array) $this->db->loadObjectList() as $widget) {
			$widgets[] = new WidgetkitWidget($widget->id, $widget->type, $widget->style, $widget->name, $widget->content, $widget->created, $widget->modified);
		}

		return $widgets;
	}

	/*
		Function: save
			Save a widget instance, returns widget instance id.

		Parameters:
			$data - Widget data

		Returns:
			Int
	*/
	public function save($data) {

		// convert numeric strings to real integers		
		if (isset($data["settings"]) && is_array($data["settings"])) {
			$data["settings"] = array_map(create_function('$item', 'return is_numeric($item) ? (float)$item : $item;'),$data["settings"]); 
		}

		// init vars
		$obj  = array(
			'name'     => $data['name'],
			'type'     => $data['type'],
			'style'    => $data['style'],
			'content'  => json_encode($data),
			'modified' => JFactory::getDate()->toMySQL()
		);

		// is update or insert ?
		if (isset($data['id']) && $data['id']) {
			$obj = (object) array_merge($obj, array('id' => $data['id']));
			$this->db->updateObject('#__widgetkit_widget', $obj, 'id');
		} else {
			$obj = (object) array_merge($obj, array('created' => $obj['modified']));
			$this->db->insertObject('#__widgetkit_widget', $obj, 'id');
		}

		return $obj->id;
	}

	/*
		Function: delete
			Delete a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			Void
	*/
	public function delete($id) {

		// set query
		$this->db->setQuery('DELETE FROM #__widgetkit_widget WHERE id='.(int) $id);
		
		return $this->db->query();
	}
	
	/*
		Function: copy
			Copy a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			Void
	*/
	public function copy($id) {

		// get widget
		$widget = $this->get($id);

		// set data
		$data = json_decode((string) $widget->content, true);
		$data['id'] = 0;
		$data['name'] .= ' (Copy)';

		return $this->save($data);
	}
	
	/*
		Function: render
			Render a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			String
	*/
	public function render($id) {

		if ($widget = $this->get((int) $id)) {

			// on render event
			$this['event']->trigger('render', array($widget));
			
			$output = $this[$widget->type]->render($widget);

			$this['event']->trigger('widgetoutput', array(&$output));

			return $output;
		}

		return false;
	}	

}

/*
	Class: WidgetkitWidget
		The Widget class.
*/
class WidgetkitWidget {
	
	/* identifier */	
	public $id;
	
	/* type */	
	public $type;

	/* style */	
	public $style;

	/* name */	
	public $name;
	
	/* content */	
	public $content;

	/* created at */
	public $created;

	/* modified at */
	public $modified;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($id, $type, $style, $name, $content, $created, $modified) {

		$widgetkit = Widgetkit::getInstance();

		// init vars
		$this->id       = $id;
		$this->type     = $type;
		$this->name     = $name;
		$this->content  = $widgetkit["data"]->create($content);
		$this->created  = $created;
		$this->modified = $modified;

	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->content->has($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->content->get($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->content->set($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function __unset($name) {
		$this->content->remove($name);
	}

}