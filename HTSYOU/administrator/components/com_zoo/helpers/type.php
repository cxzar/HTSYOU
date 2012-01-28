<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: TypeHelper
   The Helper Class for item
*/
class TypeHelper extends AppHelper {

	/*
		Function: setUniqueIndentifier
			Sets a unique type identifier

		Parameters:
			$type - Type object

		Returns:
			Type
	*/
	public function setUniqueIndentifier($type) {
		if ($type->id != $type->identifier) {
			// check identifier
			$tmp_identifier = $type->identifier;

			// build resource
			$resource = $type->getApplication()->getResource() .'types/';

			$i = 2;
			while ($this->app->path->path($resource.$tmp_identifier.'.config')) {
				$tmp_identifier = $type->identifier . '-' . $i++;
			}
			$type->identifier = $tmp_identifier;
		}
		return $type;
	}

	/*
		Function: sanatizePositionsConfig
			Sanatize positions config file (after renaming or deleting a type)

		Parameters:
			$path - Path to renderer
			$type - The type to sanatize
			$delete - if set to true, type will be removed

		Returns:
			void
	*/
	public function sanatizePositionsConfig($path, $type, $delete = false) {

		// get renderer
		$renderer = $this->app->renderer->create('item')->addPath($path);

		// get group
		$group = $type->getApplication()->getGroup();

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type->id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			if ($delete) {
				JFolder::delete($folder.$type->id);
			} else {
				JFolder::move($folder.$type->id, $folder.$type->identifier);
			}
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($group.'.'.$type->id.'.');
		if (!$delete) {
			$config->set($group.'.'.$type->identifier.'.', $params);
		}
		$config->remove($group.'.'.$type->id.'.');
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

	}

	/*
		Function: copyPositionsConfig
			Copy positions config file

		Parameters:
			$id	  - the old type id
			$path - Path to renderer
			$type - The type to copy

		Returns:
			void
	*/
	public function copyPositionsConfig($id, $path, $type) {

		// get renderer
		$renderer = $this->app->renderer->create('item')->addPath($path);

		// get group
		$group = $type->getApplication()->getGroup();

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			JFolder::copy($folder.$id, $folder.$type->id);
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($group.'.'.$id.'.');
		$config->set($group.'.'.$type->id.'.', $params);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

	}

}