<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: EditorHelper
   The Helper Class for editor
*/
class EditorHelper extends AppHelper {

	protected $_asset_id;

	public function display($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null) {
		if (!$this->app->joomla->isVersion('1.5')) {

			if ($asset === null) {
				if (!isset($this->_asset_id)) {
					$this->_asset_id= $this->app->database->queryResult('SELECT id FROM #__assets WHERE name = ' . $this->app->database->quote($this->app->component->self->name));
				}
				$asset = $this->_asset_id;
			}

			if ($author === null) {
				$author = $this->app->user->get()->id;
			}

			return $this->app->system->editor->display($name, $content, $width, $height, $col, $row, $buttons, $id, $asset, $author);
		}

		return $this->app->system->editor->display($name, $content, $width, $height, $col, $row, $buttons);

	}

}