<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(dirname(__FILE__).'/config.php');

// get zoo instance
$zoo = App::getInstance('zoo');

// set zoo icon
$zoo->set('icon', 'zoo.png');

// add css, js
$zoo->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
$zoo->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
$zoo->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
$zoo->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
$zoo->document->addScript('assets:js/accordionmenu.js');
$zoo->document->addScript('assets:js/placeholder.js');
$zoo->document->addScript('assets:js/default.js');
$zoo->document->addStylesheet('assets:css/ui.css');

// add behavior modal
$zoo->html->_('behavior.modal', 'a.modal');

// init vars
$controller = $zoo->request->getWord('controller');
$task       = $zoo->request->getWord('task');
$group      = $zoo->request->getString('group');

// does the zoo require to be updated?
if ($zoo->update->required() && $controller != 'update') {
	$zoo->system->application->redirect($zoo->link(array('controller' => 'update'), false));
}

// check if update is available
$zoo->update->available();

// cache writable ?
if (!($cache_path = $zoo->path->path('cache:')) || !is_writable($cache_path)) {
	$zoo->error->raiseError(sprintf("Zoo cache folder is not writable! Please check directory permissions (%s)", $cache_path));
}

// change application
if ($id = $zoo->request->getInt('changeapp')) {
	$zoo->system->application->setUserState('com_zooapplication', $id);
}

// load application
$application = $zoo->zoo->getApplication();

// set default controller
if (!$controller) {
	$controller = $application ? 'item' : 'new';
	$zoo->request->setVar('controller', $controller);
}

// set toolbar button include path
$toolbar = JToolBar::getInstance('toolbar');
$toolbar->addButtonPath($zoo->path->path('joomla:button'));

// build menu
$menu = $zoo->menu->get('nav');

// add "app" menu items
foreach ($zoo->table->application->all(array('order' => 'name')) as $instance) {
	$instance->addMenuItems($menu);
}

// add "new" and "manager" menu item
$new = $zoo->object->create('AppMenuItem', array('new', '<span class="icon"> </span>', $zoo->link(array('controller' => 'new')), array('class' => 'new')));
$manager = $zoo->object->create('AppMenuItem', array('manager', '<span class="icon"> </span>', $zoo->link(array('controller' => 'manager')), array('class' => 'config')));
$menu->addChild($new);
$menu->addChild($manager);

if ($controller == 'new' && $task == 'add' && $group) {
	// add info item
	$new->addChild($zoo->object->create('AppMenuItem', array('new', $zoo->object->create('Application')->setGroup($group)->getMetaData('name'))));
}

if ($controller == 'manager' && $group) {
	// add info item
	$link = $zoo->link(array('controller' => 'manager', 'task' => 'types', 'group' => $group));
	$info = $zoo->object->create('AppMenuItem', array('manager-types', $zoo->object->create('Application')->setGroup($group)->getMetaData('name'), $link));
	$info->addChild($zoo->object->create('AppMenuItem', array('manager-types', 'Types', $link)));
	$info->addChild($zoo->object->create('AppMenuItem', array('manager-info', 'Info', $zoo->link(array('controller' => 'manager', 'task' => 'info', 'group' => $group)))));
	$manager->addChild($info);
}

try {

	if ($application) {

		// dispatch current application
		$application->dispatch();

	} else {

		// dispatch app
		$zoo->dispatch();
	}

} catch (AppException $e) {
	$zoo->error->raiseError(500, $e);
}