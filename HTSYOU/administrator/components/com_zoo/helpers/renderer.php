<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: RendererHelper
   	  Renderer helper class.
*/
class RendererHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:renderer'), 'renderer');

	}

	/*
		Function: create
			Creates a Renderer instance

		Parameters:
			$type - Renderer type

		Returns:
			AppRenderer
	*/
	public function create($type = '', $args = array()) {

		// load renderer class
		$class = $type ? $type.'Renderer' : 'AppRenderer';
		if ($type) {
			$this->app->loader->register($class, 'renderer:'.strtolower($type).'.php');
		}

		// prepend app
		array_unshift($args, $this->app);

		return $this->app->object->create($class, $args);

	}

}

/*
	Class: AppRenderer
		The general class for rendering objects.
*/
class AppRenderer {

	protected $_path;
	protected $_layout_paths;
	protected $_layout;
	protected $_folder = 'renderer';
	protected $_separator = '.';
	protected $_extension = '.php';
	protected $_metafile = 'metadata.xml';

    /*
		Variable: app
			App instance.
    */
	public $app;

	const MAX_RENDER_RECURSIONS = 100;

	public function  __construct($app, $path = null) {
		$this->_layout_paths = array();
		$this->_path = $path ? $path : $app->object->create('PathHelper', array($app));
	}

	/*
		Function: render
			Render objects using a layout file.

		Parameters:
			$layout - Layout name.
			$args - Arguments to be passed to into the layout scope.

		Returns:
			String
	*/
	public function render($layout, $args = array()) {

		// prevent render to recurse indefinitely
		static $count = 0;
		$count++;

		if ($count < self::MAX_RENDER_RECURSIONS) {

			// render layout
			if ($__layout = $this->_getLayoutPath($layout)) {

				// import vars and layout output
				extract($args);
				ob_start();
				include($__layout);
				$output = ob_get_contents();
				ob_end_clean();

				$count--;

				return $output;
			}

			$count--;

			// raise warning, if layout was not found
			JError::raiseWarning(0, 'Renderer Layout "'.$layout.'" not found. ('.$this->app->utility->debugInfo(debug_backtrace()).')');

			return null;
		}

		// raise warning, if render recurses indefinitly
		JError::raiseWarning(0, 'Warning! Render recursed indefinitly. ('.$this->app->utility->debugInfo(debug_backtrace()).')');

		return null;
	}

	protected function _getLayoutPath($layout) {

		if (!isset($this->_layout_paths[$layout])) {
			// init vars
			$parts = explode($this->_separator, $layout);
			$this->_layout = preg_replace('/[^A-Z0-9_\.-]/i', '', array_pop($parts));
			$this->_layout_paths[$layout] = $this->_path->path(implode('/', $parts).'/'.$this->_layout.$this->_extension);
		}

		return $this->_layout_paths[$layout];

	}

	/*
		Function: addPath
			Add layout path(s) to renderer.

		Parameters:
			$paths - String or array of paths.

		Returns:
			Renderer
	*/
	public function addPath($paths) {

		$paths = (array) $paths;

		foreach ($paths as $path) {
			$path = rtrim($path, "\\/") . '/';
			$this->_path->register($path . $this->_folder);
		}

		return $this;

	}

	/*
		Function: getLayouts
			Retrieve an array of layout filenames.

		Returns:
			Array
	*/
	public function getLayouts($dir) {

		// init vars
		$layouts = array();

		// find layouts in path(s)
		$layouts = $this->_path->files("$dir", false, '/' . preg_quote($this->_extension) . '$/i');

		return array_map(create_function('$layout', 'return basename($layout, "'.$this->_extension.'");'), $layouts);
	}

	/*
		Function: getLayoutMetaData
			Retrieve metadata array of a layout.

		Returns:
			Array
	*/
	public function getLayoutMetaData($layout) {

		// init vars
		$metadata = $this->app->object->create('AppData');
		$parts    = explode($this->_separator, $layout);
		$name     = array_pop($parts);

		if ($file = $this->_path->path(implode(DIRECTORY_SEPARATOR, $parts).'/'.$this->_metafile)) {
			if ($xml = simplexml_load_file($file)) {
				foreach ($xml->children() as $child) {
					$attributes = $child->attributes();
					if ($child->getName() == 'layout' && (string) $attributes->name == $name) {

						foreach ($attributes as $key => $attribute) {
							$metadata[$key] = (string) $attribute;
						}

						$metadata['layout'] = $layout;
						$metadata['name'] = (string) $child->name;
						$metadata['description'] = (string) $child->description;

						break;
					}
				}
			}
		}

		return $metadata;
	}

	/*
		Function: getFolder
			Retrieve the renderers folder.

		Returns:
			String
	*/
	public function getFolder() {
		return $this->_folder;
	}

	/*
		Function: _getPath
			Retrieve paths where to find the layout files.

		Returns:
			Array
	*/
	protected function _getPath($dir = '') {
		return $this->_path->path($dir);
	}

}

/*
	Class: PositionRenderer
		The base class for rendering positions based on config files.
*/
abstract class PositionRenderer extends AppRenderer {

    protected $_config;
	protected $_config_file = 'positions.config';
	protected $_xml_file = 'positions.xml';

	/*
		Function: getPositions
			Retrieve positions of a layout.

		Parameter:
			$dir - point separated path to layout, last part is layout

		Returns:
			Array
	*/
	public function getPositions($dir) {

		// init vars
		$positions = array();

		$parts  = explode('.', $dir);
		$layout = array_pop($parts);
		$path   = implode('/', $parts);

		// parse positions xml
		if ($xml = simplexml_load_file($this->_getPath($path.'/'.$this->_xml_file))) {
			foreach ($xml->children() as $pos) {
				if ((string) $pos->attributes()->layout == $layout) {
					$positions['name'] = $layout;

					foreach ($pos->children() as $child) {

						if ($child->getName() == 'name') {
							$positions['name'] = (string) $child;
						}

						if ($child->getName() == 'position') {
							if ($child->attributes()->name) {
								$name = (string) $child->attributes()->name;
								$positions['positions'][$name] = (string) $child;
							}
						}
					}

					break;
				}
			}
		}

		return $positions;
	}

	/*
		Function: getConfig
			Retrieve position configuration.

		Parameter:
			$dir - path to config file

		Returns:
			AppData
	*/
	public function getConfig($dir) {

		// config file
		if (empty($this->_config)) {

			if ($file = $this->_path->path($dir.'/'.$this->_config_file)) {
				$content = JFile::read($file);
			} else {
				$content = null;
			}

			$this->_config = $this->app->parameter->create($content);
		}

		return $this->_config;
	}

	/*
		Function: saveConfig
			Save position configuration.

		Parameter:
			$config - Configuration
			$file - File to save configuration

		Returns:
			Boolean
	*/
	public function saveConfig($config, $file) {

		if (JFile::exists($file) && !is_writable($file)) {
			throw new AppException(sprintf('The config file is not writable (%s)', $file));
		}

		if (!JFile::exists($file) && !is_writable(dirname($file))) {
			throw new AppException(sprintf('Could not create config file (%s)', $file));
		}

		// Joomla 1.6 JFile::write expects $buffer to be reference
		$config_string = (string) $config;
		return JFile::write($file, $config_string);
	}

	public function pathExists($dir) {
		return (bool) $this->_getPath($dir);
	}

}