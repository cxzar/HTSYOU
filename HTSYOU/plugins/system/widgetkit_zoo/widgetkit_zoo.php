<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemWidgetkit_Zoo extends JPlugin {

	/* widgetkit */
	public $widgetkit;

	/* ZOO app */
	public $zoo;

	/**
	 * onAfterInitialise handler
	 *
	 * Adds Widgetkit event listeners
	 *
	 * @access	public
	 * @return null
	 */
	public function onAfterInitialise() {

		// make sure Widgetkit and ZOO exist
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_widgetkit/classes/widgetkit.php')
				|| !JComponentHelper::getComponent('com_widgetkit', true)->enabled
				|| !JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php')
				|| !JComponentHelper::getComponent('com_zoo', true)->enabled) {
			return;
		}

		// load widgetkit
		require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/classes/widgetkit.php');

		// get widgetkit instance
		$this->widgetkit = Widgetkit::getInstance();

		// load zoo
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// check if Zoo > 2.4 is loaded
		if (!class_exists('App')) {
			return;
		}

		// get zoo instance
		$this->zoo = App::getInstance('zoo');

		// check if Zoo > 2.5
		if (version_compare($this->zoo->zoo->version(), '2.5') < 0) {
			return;
		}

		// load zoo language file
		$this->zoo->system->language->load('com_'.$this->zoo->id);

		// register plugin paths
		$path = $this->zoo->path->path('plugins:system/widgetkit_zoo/');
		$this->widgetkit['path']->register($path, 'plugin.root');
		$this->widgetkit['path']->register($path.'/widgets', 'zoowidgets');
		$this->widgetkit['path']->register($path.'/assets', 'assets');

		// register widgetkit element path
		$this->zoo->path->register($path.'/elements', 'elements');

		// bind init event
		$this->widgetkit['event']->bind('admin', array($this, 'init'));
		$this->widgetkit['event']->bind('site', array($this, 'init'));
		$this->widgetkit['event']->bind('site', array($this, 'loadAssets'));

		// bind zoo type events
		$this->zoo->event->dispatcher->connect('layout:init', array($this, 'initTypeLayouts'));
		$this->zoo->event->dispatcher->connect('type:beforesave', array($this, 'beforeTypeSave'));
		$this->zoo->event->dispatcher->connect('type:copied', array($this, 'typeCopied'));
		$this->zoo->event->dispatcher->connect('type:deleted', array($this, 'typeDeleted'));

	}

	/*
		Function: init
			Init Admin Widgets

		Returns:
			void
	*/
	public function init() {
		// require widget files
		foreach ($this->widgetkit['path']->dirs('zoowidgets:') as $widget) {
			if ($file = $this->widgetkit['path']->path("zoowidgets:{$widget}/{$widget}.php")) {
				// require widget file
				require_once($file);
			}
		}
	}

	/*
		Function: loadAssets
			Load widgets css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->widgetkit['asset']->addFile('css', 'assets:css/style.css');
	}

	/*
		Function: initTypeLayouts
			Callback function for the zoo layouts

		Returns:
			void
	*/
	public function initTypeLayouts($event) {

		$extensions = (array) $event->getReturnValue();

		// add zoo widgetkit widgets to modules
		$this->widgets = array();

		foreach ($this->widgetkit['path']->dirs("zoowidgets:") as $widget) {
			if ($this->widgetkit['path']->path("zoowidgets:$widget/renderer")) {
				$extensions[] = array('name' => JText::_('Widget') . ' ' . ucfirst($widget), 'path' => $this->widgetkit['path']->path("zoowidgets:$widget"));
			}
		}

		$event->setReturnValue($extensions);

	}

	/*
		Function: beforeTypeSave
			Callback function for the zoo type save event

		Returns:
			void
	*/
	public function beforeTypeSave($event) {

		$type = $event->getSubject();

		// clean and save layout positions
		if (!empty($type->id) && $type->id != $type->identifier) {

			// update modules
			foreach ($this->widgetkit['path']->dirs("zoowidgets:") as $widget) {
				if ($this->widgetkit['path']->path("zoowidgets:$widget/renderer")) {
					$this->zoo->type->sanatizePositionsConfig($this->widgetkit['path']->path("zoowidgets:$widget"), $type);
				}
			}

		}

	}

	/*
		Function: typeCopied
			Callback function for the zoo type copied event

		Returns:
			void
	*/
	public function typeCopied($event) {

		$type = $event->getSubject();

		$old_id = $event['old_id'];

		// copy module positions
		foreach ($this->widgetkit['path']->dirs("zoowidgets:") as $widget) {
			if ($this->widgetkit['path']->path("zoowidgets:$widget/renderer")) {
				$this->zoo->type->copyPositionsConfig($old_id, $this->widgetkit['path']->path("zoowidgets:$widget"), $type);
			}
		}

	}

	/*
		Function: typeDeleted
			Callback function for the zoo type deleted event

		Returns:
			void
	*/
	public function typeDeleted($event) {

		$type = $event->getSubject();

		// remove module positions
		foreach ($this->widgetkit['path']->dirs("zoowidgets:") as $widget) {
			if ($this->widgetkit['path']->path("zoowidgets:$widget/renderer")) {
				$this->zoo->type->sanatizePositionsConfig($this->widgetkit['path']->path("zoowidgets:$widget"), $type, true);
			}
		}

	}

}

/*
	Class: ZooWidget
		Zoo Widget base class
*/
class ZooWidget {

	/* ZOO app */
	public $zoo;

	/* widgetkit */
	public $widgetkit;

	/* type */
	public $type;

	/* options */
	public $options;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct() {

		// init vars
		$this->zoo = App::getInstance('zoo');
		$this->widgetkit = Widgetkit::getInstance();
		$this->type    = strtolower(str_replace('Zoo', '', get_class($this)));
		$this->options = $this->widgetkit['system']->options;

		// bind events
		$this->widgetkit['event']->bind('dashboard', array($this, 'dashboard'));
		$this->widgetkit['event']->bind("render", array($this, 'render'));
		$this->widgetkit['event']->bind("task:edit_{$this->type}_zoo", array($this, 'edit'));
		$this->widgetkit['event']->bind("task:save_{$this->type}_zoo", array($this, 'save'));

		// register path
         $this->widgetkit['path']->register($this->widgetkit['path']->path('zoowidgets:'.$this->type), "zoo{$this->type}");
 	}

	/*
		Function: dashboard
			Render dashboard layout

		Returns:
			Void
	*/
	public function dashboard() {

		// add js
        $this->widgetkit['asset']->addFile('js', 'assets:js/dashboard.js');

		$widget_ids = array();
		foreach ($this->widgetkit['widget']->all($this->type) as $widget) {
			if (isset($widget->zoo)) {
				$widget_ids[] = $widget->id;
			}
		}

		$this->widgetkit['asset']->addString('js', 'jQuery(function($) { $(\'div.dashboard #'.$this->type.'\').ZooDashboard({edit_ids : '.json_encode($widget_ids).'}); });');

	}

	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null) {

		// load tooltip behavior
		$this->zoo->html->_('behavior.tooltip');

		// get xml settings and widget
		$xml    = simplexml_load_file($this->widgetkit['path']->path("{$this->type}:{$this->type}.xml"));
		$widget = $this->widgetkit[$this->type]->get($id ? $id : $this->widgetkit['request']->get('id', 'int'));

		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		$style_xml = simplexml_load_file($this->widgetkit['path']->path("{$this->type}:styles/{$style}/config.xml"));

		$params = isset($widget->zoo['params']) ? $widget->zoo['params'] : array();

		// get form
		$form = $this->zoo->parameterform->create($this->widgetkit['path']->path("zoo{$this->type}:{$this->type}.xml"));
		$form->setValues($params);
		$form->addElementPath($this->widgetkit['path']->path('plugin.root:layouts/fields'));

		$type = $this->type;

		echo $this->widgetkit['template']->render("plugin.root:layouts/edit", compact('widget', 'xml', 'style_xml', 'form', 'type'));
	}

	/*
		Function: render
			Render widget on site

		Returns:
			String
	*/
	public function render($widget) {

		if (isset($widget->zoo['params']) && $widget->type == $this->type) {

			$widget->items = array();

			$params = $this->zoo->data->create($widget->zoo['params']);

			if ($application = $this->zoo->table->application->get($params->get('application', 0))) {

				// load template
				if (($zoo_items = $this->zoo->module->getItems($params)) && !empty($zoo_items)) {

					// set renderer
					$renderer = $this->zoo->renderer->create('item')->addPath(array($this->zoo->path->path('component.site:'), $this->widgetkit['path']->path('zoowidgets:'.$widget->type)));

					$i = 0;
					$widget_items = array();
					foreach($zoo_items as $item) {

						// add the item itself
						$widget_items[$i]['zooitem'] = $item;

						// add item title
						$widget_items[$i]['title'] = $item->name;

						// add item content
						$widget_items[$i]['content'] = $renderer->render('item.'.$params->get('layout'), compact('item', 'params'));

						// add item special positions
						$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
						$style_xml = simplexml_load_file($this->widgetkit['path']->path("{$this->type}:styles/{$style}/config.xml"));
						foreach ($style_xml->xpath('fields/field') as $field) {
							$name = (string) $field->attributes()->name;
							if (!in_array($name, array('title', 'content'))) {
								$widget_items[$i][$name] = $renderer->checkPosition($name) ? $renderer->renderPosition($name) : '';
							}
						}

						$i++;
					}
					$widget->items = $widget_items;
				}
			}
		}

	}

	/*
		Function: save
			Save action

		Returns:
			Void
	*/
	public function save() {

		// save data
		$data['type']     = $this->type;
		$data['id']       = $this->widgetkit['request']->get('id', 'int');
		$data['name']     = $this->widgetkit['request']->get('name', 'string');
		$data['settings'] = $this->widgetkit['request']->get('settings', 'array');
		$data['style']    = $this->widgetkit['request']->get('settings.style', 'array');
		$data['zoo']	  = $this->widgetkit['request']->get('zoo', 'array');

		// convert numeric strings to real integers
		if (isset($data["settings"]) && is_array($data["settings"])) {
			$data["settings"] = array_map(create_function('$item', 'return is_numeric($item) ? (float)$item : $item;'), $data["settings"]);
		}

		$this->edit($this->widgetkit['widget']->save($data));
	}

}