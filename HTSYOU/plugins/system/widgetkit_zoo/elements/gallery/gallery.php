<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: ElementGallery
		The file element class
*/
class ElementGallery extends Element {

	protected $filter = "/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i";

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set callbacks
		$this->registerCallback('dirs');
	}

	/*
		Function: getDirectory
			Returns the directory with trailing slash.

		Returns:
			String - directory
	*/
	public function getDirectory() {
		return rtrim($this->config->get('directory'), '/').'/';
	}

	public function getResource() {
		return 'root:'.$this->getDirectory().trim($this->get('value'), '/');
	}

	public function getFiles() {
		return $this->app->path->files($this->getResource(), false, $this->filter);
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		// init vars
		$params = $this->app->data->create($params);
		$value = $this->get('value');
		$thumbs = $this->_getThumbnails($params);
		return !empty($value) && !empty($thumbs);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$params = $this->app->data->create($params);

		// get thumbnails
		$thumbs = $this->_getThumbnails($params);

		// no thumbnails found
		if (empty($thumbs)) {
			return JText::_('No thumbnails found');
		}

		// limit thumbnails to count
		if (($count = (int) $params->get('count', 0)) && $count < count($thumbs)) {
			$thumbs = array_slice($thumbs, 0, $count);
		}

		// add css and javascript
		$this->app->document->addScript('elements:gallery/gallery.js');
		$this->app->document->addStylesheet('elements:gallery/gallery.css');

		if ($layout = $this->getLayout($params->get('mode', 'lightbox').'.php')) {
			return $this->renderLayout($layout, compact('thumbs', 'params'));
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$title = htmlspecialchars(html_entity_decode($this->get('title'), ENT_QUOTES), ENT_QUOTES);

		if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout, compact('title'));
        }

        return null;

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->app->document->addScript('assets:js/finder.js');
		$this->app->document->addScript('elements:gallery/gallery.js');
	}

	/*
		Function: dirs
			Get directory list JSON formatted

		Returns:
			Void
	*/
	public function dirs() {
		$dirs = array();
		$path = $this->app->request->get('path', 'string');
		foreach ($this->app->path->dirs('root:'.$this->getDirectory().$path) as $dir) {
			$count = count($this->app->path->files('root:'.$this->getDirectory().$path.'/'.$dir, false, $this->filter));
			$dirs[] = array('name' => basename($dir) . " ($count)", 'path' => $path.'/'.$dir, 'type' => 'folder');
		}

		return json_encode($dirs);
	}

	protected function _getThumbnails($params) {

		$thumbs     = array();
		$width      = (int) $params->get('width');
		$height     = (int) $params->get('height');
		$title      = $this->get('title', '');
		$path		= $this->app->path->path($this->getResource()).'/';

		// set default thumbnail size, if incorrect sizes defined
		if ($width < 1 && $height < 1) {
			$width  = 100;
			$height = null;
		}

		foreach ($this->getFiles() as $filename) {

			$file  = $path.$filename;
			$thumb = $this->app->zoo->resizeImage($file, $width, $height);

			// if thumbnail exists, add it to return value
			if (is_file($thumb)) {

				// set image name or title if exsist
				$name = !empty($title) ? $title : $this->app->string->ucwords($this->app->string->str_ireplace('_', ' ', JFile::stripExt($filename)));

				// get image info
				list($thumb_width, $thumb_height) = @getimagesize($thumb);

				$thumbs[] = array(
					'name'         => $name,
					'filename'     => $filename,
					'img'          => JURI::root().$this->app->path->relative($file),
					'img_file'     => $file,
					'thumb'		   => JURI::root().$this->app->path->relative($thumb),
					'thumb_width'  => $thumb_width,
					'thumb_height' => $thumb_height
				);
			}
		}

		usort($thumbs, create_function('$a,$b', 'return strcmp($a["filename"], $b["filename"]);'));
		switch ($params->get('order', 'asc')) {
			case 'random':
				shuffle($thumbs);
				break;
			case 'desc':
				$thumbs = array_reverse($thumbs);
				break;
		}

		return $thumbs;
	}

}