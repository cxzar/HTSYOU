<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load installer
require_once(dirname(__FILE__).'/installer.php');

// run installer script
$script = new InstallerScript();
$script->install($this);

// remove link
$db = JFactory::getDBO();
$db->setQuery("UPDATE #__components AS c SET c.link='' WHERE c.option='com_widgetkit'");
$db->query();

// update tinymce
$db->setQuery("SELECT p.params FROM #__plugins AS p WHERE p.element='tinymce'");
$data = new JParameter($db->loadResult());
$data->set('extended_elements', $data->get('extended_elements').',@[data-lightbox],@[data-spotlight]');
$db->setQuery("UPDATE #__plugins AS p SET p.params='".$data->toString()."' WHERE p.element='tinymce'");
$db->query();