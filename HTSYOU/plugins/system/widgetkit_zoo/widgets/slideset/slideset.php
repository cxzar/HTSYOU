<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: ZooSlideset
		Zoo Slideset class
*/
class ZooSlideset extends ZooWidget {

	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null) {
		$this->widgetkit['system']->document->addScriptDeclaration('jQuery(function($) { $(\'[name="settings[items_per_set]"] option[value="set"]\').attr("disabled", "disabled"); });');
		parent::edit($id);
	}

}

// instantiate ZooSlideset
new ZooSlideset();