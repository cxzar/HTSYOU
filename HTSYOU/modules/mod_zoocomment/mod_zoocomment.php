<?php
/**
* @package   ZOO Comment
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// get app
$zoo = App::getInstance('zoo');

// load zoo frontend language file
$zoo->system->language->load('com_zoo');

// init vars
$path = dirname(__FILE__);

//register base path
$zoo->path->register($path, 'mod_zoocomment');

// register helpers
$zoo->path->register($path, 'helpers');
$zoo->loader->register('CommentModuleHelper', 'helpers:helper.php');

// init vars
$application = $zoo->table->application->get($params->get('application', 0));

// is application ?
if (empty($application)) {
	return null;
}

// set one or multiple categories
$category = $params->get('category', 0);
if ($params->get('subcategories')) {
	$categories = $application->getCategoryTree(true);
	if (isset($categories[$category])) {
		$category = array_merge(array($category), array_keys($categories[$category]->getChildren(true)));
	}
}

// get latest comments
$comments = $zoo->commentmodule->getLatestComments($application, $category, $params->get('count', 10));

// load template

if (!empty($comments)) {

	include(JModuleHelper::getLayoutPath('mod_zoocomment', $params->get('theme', 'list-v')));

}