<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: SystemWidgetkitHelper
		System helper class
*/
class SystemWidgetkitHelper extends WidgetkitHelper {

	/* application */
	public $application;

	/* document */
	public $document;

	/* language */
	public $language;

	/* system path */
	public $path;

	/* system url */
	public $url;
	
	/* options */
	public $options;

	/* cache path */
	public $cache_path;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// init vars
		$this->application = JFactory::getApplication();
        $this->document    = JFactory::getDocument();
		$this->language    = JFactory::getLanguage();
        $this->path        = JPATH_ROOT;
        $this->url         = rtrim(JURI::root(false), '/');
		$this->options     = $this['data']->create($this->_getParams());
        $this->cache_path  = $this->path.'/cache/widgetkit';

		// set cache directory
		if (!file_exists($this->cache_path)) {
			JFolder::create($this->cache_path);
		}
	}

	/*
		Function: init
			Initialize system
		
		Returns:
			Void
	*/
	public function init() {

		// set translations
		$this->language->load('widgetkit', $this['path']->path('widgetkit:'), null, true);

		// set paths
        $this['path']->register($this->path, 'site');
        $this['path']->register($this->path.'/media/widgetkit', 'widgetkit');
        $this['path']->register($this->path.'/media/widgetkit/widgets', 'widgets');
        $this['path']->register($this->path.'/modules', 'modules');
        $this['path']->register($this->path.'/'.JComponentHelper::getParams('com_media')->get('file_path'), 'media');
        $this['path']->register($this->cache_path, 'cache');
		
		// load widgets
		foreach ($this['path']->dirs('widgets:') as $name) {
			if ($file = $this['path']->path("widgets:{$name}/{$name}.php")) {
				require_once($file);
			}
		}

		// is admin ?
		if ($this->application->isAdmin() && $this['request']->get('option', 'string') == 'com_widgetkit') {
			
			// cache writable ?
			if (!file_exists($this->cache_path) || !is_writable($this->cache_path)) {
				$this->application->enqueueMessage("Widgetkit cache folder is not writable! Please check directory permissions ({$this->cache_path})", 'notice');
			}
			
			// load editor
			$this['editor']->init();
			
            // add stylesheets/javascripts
			$this['asset']->addFile('css', 'widgetkit:css/admin.css');
			$this['asset']->addFile('css', 'widgetkit:css/system.css');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.ui.js');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.plugins.js');
			$this['asset']->addFile('js', 'widgetkit:js/admin.js');
			$this['asset']->addString('js', 'var widgetkitajax = "'.$this['system']->link(array('ajax' => true)).'";');
			
            // get request vars
			$task = $this['request']->get('task', 'string');

			// get version
			$this["version"] = ($path = $this['path']->path('widgetkit:widgetkit.xml')) && ($xml = simplexml_load_file($path)) ? (string) $xml->version[0] : '';

			// trigger event
			$this['event']->trigger('admin');

			// execute task
			echo $this['template']->render($task ? 'task' : 'dashboard', compact('task'));

			// add assets
			$this['template']->render('assets');

			// check for updates
			if($xmlpath = $this['path']->path('widgetkit:widgetkit.xml')){
			
				$xml = $this['dom']->create($xmlpath, 'xml');

				// update check
				if ($url = $xml->first('updateUrl')->text()) {

					// create check url
					$url = sprintf('%s?application=%s&version=%s&format=raw', $url, 'widgetkit_j17', urlencode($xml->first('version')->text()));

					// only check once a day
					$hash = md5($url.date('Y-m-d'));
					if ($this['option']->get("update_check") != $hash) {
						if ($request = $this['http']->get($url)) {
							$this['option']->set("update_check", $hash);
							$this['option']->set("update_data", $request['body']);
						}
					}

					// decode response and set message
					if (($data = json_decode($this['option']->get("update_data"))) && $data->status == 'update-available') {
						$this->application->enqueueMessage($data->message, 'notice');
					}

				}
				
			}

		}

		// is site ?
		if ($this->application->isSite() && is_a($this->document, 'JDocumentHTML')) {

			$this['asset']->addString("js", 'window["WIDGETKIT_URL"]="'.$this['path']->url("widgetkit:").'";');
			$this['asset']->addString("js", 'function wk_ajax_render_url(widgetid){ return "'.JRoute::_("index.php?option=com_widgetkit&tmpl=raw&id=").'"+widgetid}');
			
			// set direction
			$this->options->set('direction', $this->document->direction);

            // add stylesheets/javascripts
			$this['asset']->addFile('css', 'widgetkit:css/widgetkit.css');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.plugins.js');

			// trigger event
			$this['event']->trigger('site');

			// add assets
			$this['template']->render('assets');

			$this['event']->bind('widgetoutput', array($this,"_applycontentplugins"));
		}

	}

	/*
		Function: link
			Get link to system related resources.

		Parameters:
			$query - HTTP query options
		
		Returns:
			String
	*/
	public function link($query = array()) {

		// build query
		$query = array_merge(array('option' => $this['request']->get('option', 'string')), $query);

		if (isset($query['ajax'])) {
			$query = array_merge(array('format' => 'raw'), $query);
		}

		return $this->url.'/administrator/index.php?'.http_build_query($query, '', '&');
	}
	
	/*
		Function: saveOptions
			Save plugin options

		Returns:
			Void
	*/
	public function saveOptions() {
		$this->_setParams((string) $this->options);		
	}
	
	/*
		Function: __
			Retrieve translated strings

		Returns:
			String
	*/	
    public function __($string) {
		return JText::_($string);
    }

	/*
		Function: _getParams
			Get parameter from database

		Returns:
			String
	*/
	protected function _getParams() {

		$db = JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__extensions AS e WHERE e.element='com_widgetkit'");

		return $db->loadResult();
	}

	/*
		Function: _saveParams
			Set parameter in database

		Returns:
			Boolean
	*/
	protected function _setParams($params) {

		$db = JFactory::getDBO();
		$db->setQuery(sprintf("UPDATE #__extensions AS e SET e.params='%s' WHERE e.element='com_widgetkit'", $db->getEscaped($params)));

		return $db->query();
	}

	/*
		Function: _applycontentplugins
			Apply content plugins

		Returns:
			Void
	*/
	public function _applycontentplugins(&$text) {
		
		jimport('joomla.html.parameter');

		if(!class_exists("plgContentWidgetkit_Content")) {
			JPluginHelper::importPlugin('content');
		}

		$params        = new JParameter();
		$article       = new stdClass();
		$wkplugin      = new plgContentWidgetkit_Content(JDispatcher::getInstance());
		$posplugin     = new plgContentLoadmodule(JDispatcher::getInstance());
		$article->text = $text;
		
		$posplugin->params  = $params;
		
		$posplugin->onContentPrepare('widgetkit', $article, $params, 0);
		$wkplugin->onContentPrepare('widgetkit', $article, $params, 0);

		$text = $article->text;
	}

}