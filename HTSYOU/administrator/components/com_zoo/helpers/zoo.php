<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ZooHelper
		The general helper Class for zoo
*/
class ZooHelper extends AppHelper {

	/* application */
	protected $_application;

	/* version */
	protected $_version;

	/*
		Function: getApplication
		  Returns a reference to the currently active Application object.

	   Returns:
	      Application
	*/
	public function getApplication() {

		// check if application object already exists
		if (isset($this->_application)) {
			return $this->_application;
		}

		// get joomla and application table
		$joomla = $this->app->system->application;
		$table  = $this->app->table->application;

		// handle admin
		if ($joomla->isAdmin()) {

			// create application from user state, or search for default
			$id   = $joomla->getUserState('com_zooapplication');
			$apps = $table->all(array('order' => 'name'));

			if (isset($apps[$id])) {
				$this->_application = $apps[$id];
			} else if (!empty($apps)) {
				$this->_application = array_shift($apps);
			}

			return $this->_application;
		}

		// handle site
		if ($joomla->isSite()) {

			// get component params
			$params = $joomla->getParams();

			// create application from menu item params / request
			if ($item_id = $this->app->request->getInt('item_id')) {
				if ($item = $this->app->table->item->get($item_id)) {
					$this->_application = $item->getApplication();
				}
			} else if ($category_id = $this->app->request->getInt('category_id')) {
				if ($category = $this->app->table->category->get($category_id)) {
					$this->_application = $category->getApplication();
				}
			} else if ($submission_id = $this->app->request->getInt('submission_id')) {
				if ($submission = $this->app->table->submission->get($submission_id)) {
					$this->_application = $submission->getApplication();
				}
			} else if ($id = $this->app->request->getInt('app_id')) {
				$this->_application = $table->get($id);
			} else if ($id = $params->get('application')) {
				$this->_application = $table->get((int) $id);
			} else {
				// try to get application from default menu item
				$menu    = $this->app->object->create('JSite')->getMenu();
				$default = $menu->getDefault();
				if (isset($default->component) && $default->component == 'com_zoo') {
					if ($app_id = $menu->getParams($default->id)->get('application')) {
						$this->_application = $table->get((int) $app_id);
					}
				}
			}

			return $this->_application;
		}

		return null;
	}

	/*
		Function: toolbarHelp
			Add help button to current toolbar to show help url in popup window.

	   Parameters:
	      $ref - Help url

	   Returns:
	      Void
	*/
	public function toolbarHelp($ref = 'http://docs.yootheme.com/home/category/zoo-20') {
		JToolBar::getInstance('toolbar')->appendButton('ZooHelp', $ref);
	}

	/*
		Function: resizeImage
			Resize and cache image file.

		Returns:
			String - image path
	*/
	public function resizeImage($file, $width, $height) {

		// init vars
		$width      = (int) $width;
		$height     = (int) $height;
		$file_info  = pathinfo($file);
		$thumbfile  = $this->app->path->path('cache:').'/images/'. $file_info['filename'] . '_' . md5($file.$width.$height) . '.' . $file_info['extension'];
		$cache_time = 86400; // cache time 24h

		// check thumbnail directory
		if (!JFolder::exists(dirname($thumbfile))) {
			JFolder::create(dirname($thumbfile));
		}

		// create or re-cache thumbnail
		if ($this->app->imagethumbnail->check() && (!is_file($thumbfile) || ($cache_time > 0 && time() > (filemtime($thumbfile) + $cache_time)))) {
			$thumbnail = $this->app->imagethumbnail->create($file);

			if ($width > 0 && $height > 0) {
				$thumbnail->setSize($width, $height);
				$thumbnail->save($thumbfile);
			} else if ($width > 0 && $height == 0) {
				$thumbnail->sizeWidth($width);
				$thumbnail->save($thumbfile);
			} else if ($width == 0 && $height > 0) {
				$thumbnail->sizeHeight($height);
				$thumbnail->save($thumbfile);
			} else {
                if (JFile::exists($file)) {
                    JFile::copy($file, $thumbfile);
                }
            }

		}

		if (is_file($thumbfile)) {
			return $thumbfile;
		}

		return $file;
	}

	/*
		Function: triggerContentPlugins
			Trigger joomla content plugins on given text.

	   Parameters:
            $text - Text
			$params - Array params

		Returns:
			String - Text
	*/
	public function triggerContentPlugins($text, $params = array(), $context = 'com_zoo') {

		// import joomla content plugins
		JPluginHelper::importPlugin('content');

		$registry      = new JRegistry('');
		$registry->loadArray($params);
		$dispatcher    = JDispatcher::getInstance();
		$article       = JTable::getInstance('content');
		$article->text = $text;

		// disable loadmodule plugin on feed view
		if ($this->app->document->getType() == 'feed') {
			$plugin = JPluginHelper::getPlugin('content', 'loadmodule');
			if ($this->app->parameter->create($plugin->params)->get('enabled', 1)) {
				// expression to search for
				$regex = '/{loadposition\s*.*?}/i';
				$article->text = preg_replace($regex, '', $article->text);
			}
		}

		if ($this->app->joomla->isVersion('1.5')) {
			$dispatcher->trigger('onPrepareContent', array(&$article, &$registry, 0));
		} else {
			$dispatcher->trigger('onContentPrepare', array($context, &$article, &$registry, 0));
		}

		return $article->text;
	}

    /*
		Function: getGroups
			Returns user group objects.

		Returns:
			Array - groups
	*/
	public function getGroups() {
		if ($this->app->joomla->isVersion('1.5')) {
			return $this->app->database->queryObjectList("SELECT id, name FROM #__groups", "id");
		} else {
			return $this->app->database->queryObjectList("SELECT id, title AS name FROM #__viewlevels", "id");
		}
	}

    /*
		Function: getGroup
			Return user group object.

	   Parameters:
            $id - Id

		Returns:
			Object - group
	*/
	public function getGroup($id) {
		$groups = $this->getGroups();
		return isset($groups[$id]) ? $groups[$id] : array('id' => '', 'name' => '');
	}

    /*
		Function: getVersion
			Returns current ZOO version.

		Returns:
			String - version
	*/
	public function version() {

		if (empty($this->_version)) {
			// make sure versions table is present
			$this->app->database->query('CREATE TABLE IF NOT EXISTS '.ZOO_TABLE_VERSION.' (version varchar(255) NOT NULL) ENGINE=MyISAM;');

			$this->_version = $this->app->database->queryResult('SELECT version FROM '.ZOO_TABLE_VERSION);
		}

		return $this->_version;
	}

    /*
		Function: buildPageTitle
			Build page title from Joomla configuration.

		Returns:
			String - title
	*/
	public function buildPageTitle($title) {
		$dir = $this->app->system->application->getCfg('sitename_pagetitles', 0);
		if ($dir == 1) {
			return JText::sprintf('JPAGETITLE', $this->app->system->application->getCfg('sitename'), $title);
		} else if ($dir == 2) {
			return JText::sprintf('JPAGETITLE', $title, $this->app->system->application->getCfg('sitename'));
		}
		return $title;
	}

	// @deprecated with zoo 2.5.11 use application helper instead
	public function getApplicationGroups() {
		return $this->app->application->groups();
	}

	// @deprecated with zoo 2.5.11 use type helper instead
    public function getLayouts($application, $type_id, $layout_type = '') {
		return $this->app->type->layouts($application->getType($type_id), $layout_type);
    }

}