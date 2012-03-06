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

class plgSystemWidgetkit_Joomla extends JPlugin {

	/* widgetkit */
	public $widgetkit;

	/**
	 * onAfterInitialise handler
	 *
	 * Adds Widgetkit event listeners
	 *
	 * @access	public
	 * @return null
	 */
	public function onAfterInitialise() {

		// make sure Widgetkit
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_widgetkit/classes/widgetkit.php')
				|| !JComponentHelper::getComponent('com_widgetkit', true)->enabled) {
			return;
		}

		// load widgetkit
		require_once(JPATH_ADMINISTRATOR.'/components/com_widgetkit/classes/widgetkit.php');

		// get widgetkit instance
		$this->widgetkit = Widgetkit::getInstance();

		// register plugin paths
		$path = JPATH_ROOT.'/plugins/system/widgetkit_joomla/';
		$this->widgetkit['path']->register($path, 'widgetkit_joomla.root');
		$this->widgetkit['path']->register($path.'widgets', 'widgetkit_joomla.widgets');
		$this->widgetkit['path']->register($path.'assets', 'widgetkit_joomla.assets');

		// load helper
		require_once($path.'helper.php');

		// bind init event
		$this->widgetkit['event']->bind('admin', array($this, 'init'));
		$this->widgetkit['event']->bind('site', array($this, 'init'));
		$this->widgetkit['event']->bind('site', array($this, 'loadAssets'));
		$this->widgetkit['event']->bind('widgetoutput', array($this, '_applycontentplugins'));

	}

	/*
		Function: init
			Init Admin Widgets

		Returns:
			void
	*/
	public function init() {
		// require widget files
		foreach ($this->widgetkit['path']->dirs('widgetkit_joomla.widgets:') as $widget) {
			if ($file = $this->widgetkit['path']->path("widgetkit_joomla.widgets:{$widget}/{$widget}.php")) {
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
		$this->widgetkit['asset']->addFile('css', 'widgetkit_joomla.assets:css/style.css');
	}

	/*
		Function: _applycontentplugins
			Apply content plugins

		Returns:
			Void
	*/
	public function _applycontentplugins(&$text) {

		// import joomla content plugins
		JPluginHelper::importPlugin('content');

		$registry      = new JRegistry('');
		$dispatcher    = JDispatcher::getInstance();
		$article       = JTable::getInstance('content');
		$article->text = $text;

		$dispatcher->trigger('onPrepareContent', array(&$article, &$registry, 0));
		$dispatcher->trigger('onContentPrepare', array('com_widgetkit', &$article, &$registry, 0));

		$text = $article->text;

	}

}

/*
	Class: JoomlaWidget
		Joomla Widget base class
*/
class JoomlaWidget {

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
		$this->widgetkit = Widgetkit::getInstance();
		$this->type    = strtolower(str_replace('Joomla', '', get_class($this)));
		$this->options = $this->widgetkit['system']->options;

		// bind events
		$this->widgetkit['event']->bind('dashboard', array($this, 'dashboard'));
		$this->widgetkit['event']->bind("render", array($this, 'render'));
		$this->widgetkit['event']->bind("task:edit_{$this->type}_joomla", array($this, 'edit'));
		$this->widgetkit['event']->bind("task:save_{$this->type}_joomla", array($this, 'save'));

		// register path
         $this->widgetkit['path']->register($this->widgetkit['path']->path('widgetkit_joomla.widgets:'.$this->type), "joomla{$this->type}");
 	}

	/*
		Function: dashboard
			Render dashboard layout

		Returns:
			Void
	*/
	public function dashboard() {

		// add js
        $this->widgetkit['asset']->addFile('js', 'widgetkit_joomla.assets:js/dashboard.js');

		$widget_ids = array();
		foreach ($this->widgetkit['widget']->all($this->type) as $widget) {
			if (isset($widget->joomla)) {
				$widget_ids[] = $widget->id;
			}
		}

		$this->widgetkit['asset']->addString('js', 'jQuery(function($) { $(\'div.dashboard #'.$this->type.'\').JoomlaDashboard({edit_ids : '.json_encode($widget_ids).'}); });');

	}

	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null) {

		// get xml settings and widget
		$xml    = simplexml_load_file($this->widgetkit['path']->path("{$this->type}:{$this->type}.xml"));
		$widget = $this->widgetkit[$this->type]->get($id ? $id : $this->widgetkit['request']->get('id', 'int'));

		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		$style_xml = simplexml_load_file($this->widgetkit['path']->path("{$this->type}:styles/{$style}/config.xml"));

		// get params and xml
		$joomla_xml = simplexml_load_file($this->widgetkit['path']->path("joomla{$this->type}:{$this->type}.xml"));

		$type = $this->type;

		$this->widgetkit['path']->register($this->widgetkit['path']->path('widgetkit_joomla.root:layouts'), 'layouts');

		echo $this->widgetkit['template']->render("edit", compact('widget', 'xml', 'style_xml', 'type', 'joomla_xml'));
	}

	/*
		Function: render
			Render widget on site

		Returns:
			String
	*/
	public function render($widget) {

		if (isset($widget->joomla) && $widget->type == $this->type) {

			$widget->items = array();

			$params = $this->widgetkit['data']->create($widget->joomla);

			$items = $this->widgetkit['widgetkitjoomla']->getList($params);

			$i = 0;
			$widget_items = array();
			foreach($items as $item) {

				// add title
				$widget_items[$i]['title'] = $item->title;

				// add content
				$widget_items[$i]['content'] = $this->widgetkit['widgetkitjoomla']->renderItem($item, $params);

				$widget_items[$i]['navigation'] = '';
				$widget_items[$i]['caption'] = '';

				$i++;
			}
			$widget->items = $widget_items;
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
		$data['joomla']	  = $this->widgetkit['request']->get('joomla', 'array');

		// convert numeric strings to real integers
		if (isset($data["settings"]) && is_array($data["settings"])) {
			$data["settings"] = array_map(create_function('$item', 'return is_numeric($item) ? (float)$item : $item;'), $data["settings"]);
		}

		$this->edit($this->widgetkit['widget']->save($data));
	}

}