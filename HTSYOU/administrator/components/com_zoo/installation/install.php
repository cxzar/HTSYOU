<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$GLOBALS['ZOO_COMPONENT_INSTALLER'] = $this;

function com_install() {

	$installer = $GLOBALS['ZOO_COMPONENT_INSTALLER'];

	// init vars
	$path = dirname(dirname(__FILE__));

	// load install script
	require_once($path.'/file.script.php');

	return Com_ZOOInstallerScript::install($installer);

}