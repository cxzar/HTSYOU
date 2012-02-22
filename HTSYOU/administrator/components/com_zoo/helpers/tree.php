<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: TreeHelper
   The Helper Class for tree
*/
class TreeHelper extends AppHelper {

	/*
		Function: build
			Build tree.

		Parameters:
			$items - Items
			$classname - the objects classname

		Returns:
			Array - Category list
	*/
	public function build($items, $classname, $max_depth = 0, $parent_property = 'parent') {

		// create root category
		$root = $this->app->object->create($classname);
		$root->id = 0;
		$root->name = 'ROOT';
		$root->alias = '_root';
		$items[0] = $root;

		foreach ($items as $item) {
			// set parent and child relations
			if (isset($item->$parent_property) && isset($items[$item->$parent_property])) {
				$item->setParent($items[$item->$parent_property]);
				$items[$item->$parent_property]->addChild($item);
			}
		}

		if ($max_depth) {
			foreach ($items as $item) {
				if (count($item->getPathway()) > $max_depth) {
					$item->getParent()->removeChild($item);
					$item->setParent($items[0]);
					$items[0]->addChild($item);
				}
			}
		}
		
		return $items;
	}

	/*
		Function: buildList
			Build tree list which reflects the tree structure.

		Parameters:
			$id - Item id to start
			$items - Items collection (build with build)
			$list - Category tree list return value
			$prefix - Sublevel prefix
			$spacer - Spacer
			$indent - Indent
			$level - Start level
			$maxlevel - Maximum level depth

		Returns:
			Array - Category tree list
	*/
	public function buildList($id, $items, $list = array(), $prefix = '<sup>|_</sup>&nbsp;', $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $indent = '', $level = 0, $maxlevel = 9999) {

		if (isset($items[$id]) && $level <= $maxlevel) {
			foreach ($items[$id]->getChildren() as $child) {

				// set treename
				$id        = $child->id;
				$list[$id] = $child;
				$list[$id]->treename = $indent.($indent == '' ? $child->name : $prefix.$child->name);
				$list = $this->buildList($id, $items, $list, $prefix, $spacer, $indent.$spacer, $level+1, $maxlevel);
			}
		}

		return $list;
	}

}