<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ParameterHelper
		The general helper Class for parameter
*/
class ParameterHelper extends AppHelper {

	/*
		Function: create
			Get a ParameterData instance

		Returns:
			ParameterData
	*/
	public function create($params = array()) {
		$this->app->loader->register('JSONData', 'data:json.php');
		return $this->app->data->create($params, 'parameter');
	}

}