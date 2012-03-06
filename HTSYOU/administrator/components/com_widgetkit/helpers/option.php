<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: OptionWidgetkitHelper
		Option helper class, store option data
*/
class OptionWidgetkitHelper extends WidgetkitHelper {

    /*
		Variable: file
			Option file.
    */
	protected $file;

    /*
		Variable: data
			Option data.
    */
	protected $data;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);
		
		// load data
		$this->file = $this['system']->cache_path.'/widgetkit.php';
		$this->data = $this['data']->create(file_exists($this->file) ? file_get_contents($this->file) : null);
	}

	/*
		Function: get
			Get a value from data

		Parameters:
			$name - String
			$default - Mixed
		Returns:
			Mixed
	*/
	public function get($name, $default = null) {
		return $this->data->get($name, $default);
	}

 	/*
		Function: set
			Set a value

		Parameters:
			$name - String
			$value - Mixed

		Returns:
			Void
	*/
	public function set($name, $value) {
		$this->data->set($name, $value);
		@file_put_contents($this->file, (string) $this->data);
	}

}