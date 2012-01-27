<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class ExportHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:exporter'), 'exporter');

	}

	/*
		Function: create
			Creates a AppExporter instance

		Parameters:
			$type - AppExporter name

		Returns:
			AppExporter
	*/
	public function create($type) {

		$type	= preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

		// load renderer class
		$class = 'AppExporter' . $type;
		$this->app->loader->register($class, 'exporter:'.strtolower($type).'.php');

		return $this->app->object->create($class);
	}

	/*
		Function: create
			Get all AppExporter

		Parameters:
			$type - AppExporter name

		Returns:
			array - AppExporter instances
	*/
	public function getExporters($ignore = array()) {
		$ignore = (array) $ignore;
		$exporters = array();
		foreach ($this->app->path->files('exporter:', false, '/\.php$/') as $file) {
			if ($instance = $this->create(basename($file, '.php'))) {
				if (!in_array($instance->getName(), $ignore)) {
					$exporters[] = $instance;
				}
			}
		}
		return $exporters;
	}

}

abstract class AppExporter {

	public $app;

	protected $_data;
	protected $_name;

	public $category_attributes = array('parent', 'published', 'description', 'ordering');
	public $item_attributes = array('searchable', 'state', 'created',
										'modified', 'hits', 'author',
										'access', 'priority', 'metakey',
										'metadesc', 'metadata', 'publish_up',
										'publish_down');
	public $element_attributes = array('text' => array('value'),
										'textarea' => array('value'),
										'download' => array('file', 'download_limit', 'hits', 'size'),
										'rating' => array('value', 'votes'),
										'date' => array('value'),
										'email' => array('text', 'value', 'subject', 'body'),
										'link' => array('text', 'value', 'target', 'rel'),
										'gallery' => array('value', 'title'),
										'image' => array('file'),
										'video' => array('file', 'url', 'width', 'height', 'autoplay'),
										'joomlamodule' => array('value'),
										'socialbookmarks' => array('value'),
										'addthis' => array('value'),
										'disqus' => array('value'),
										'flickr' => array('value', 'flickrid'),
										'googlemaps' => array('location', 'popup'),
										'intensedebate' => array('value'),
										'checkbox' => array('option'),
										'radio' => array('option'),
										'select' => array('option'),
										'country' => array('country'),
										'relatedcategories' => array('category'),
										'relateditems' => array('item')
	);
	public $comment_attributes = array('parent_id', 'user_id', 'user_type',
										'author', 'email', 'url', 'ip',
										'created', 'content', 'state', 'username');


	public function __construct() {
		$this->app = App::getInstance('zoo');
		$this->_data = $this->app->data->create();
		$this->_data['categories'] = array();
		$this->_data['items'] = array();
	}

	/*
		Function: getName
			Get a AppExporter name

		Returns:
			String - name
	*/
	public function getName() {
		return $this->_name;
	}

	/*
		Function: getName
			Get a AppExporter type

		Returns:
			String - type
	*/
	public function getType() {
		return strtolower(str_replace('AppExporter', '', get_class($this)));
	}

	/*
		Function: isEnabled
			Is exporter enabled.
			May be overloaded by the child class.

		Returns:
			Boolean
	*/
	public function isEnabled() {
		return true;
	}

	/*
		Function: export
			Do the export.
			Must be overloaded by the child class.

		Returns:
			String - the export xml
	*/
	public function export() {
		return (string) $this->_data;
	}

	protected function _addCategory($name, $id = '', $data = array()) {

		if (empty($id)) {
			$id = JFilterOutput::stringURLSafe($name);
		}

		while (isset($this->_data['categories'][$id])) {
			$id .= '-2';
		}

		$data['name'] = $name;

		$this->_data['categories'][$id] = $data;

		return $this;
	}

	protected function _addItem($name, $id = '', $group = 'default', $data = array()) {

		if (empty($id)) {
			$id = JFilterOutput::stringURLSafe($name);
		}

		while (isset($this->_data['items'][$id])) {
			$id .= '-2';
		}

		$data['group'] = $group;
		$data['name'] = $name;

		$this->_data['items'][$id] = $data;

		return $this;
	}

}

/*
	Class: AppExporterException
*/
class AppExporterException extends AppException {}