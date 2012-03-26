<?php
/**
* @version 0.4.2
* @package System Message
* @copyright (C) 2010 Ferron Smith
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
 
/** ensure direct access is not attempted */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<?php	
	//Add stylesheet link to the page
	$doc = &JFactory::getDocument();
	$doc->addStyleSheet(JURI::root(true) ."/modules/mod_sys_message/settings/settings.css");

	//get user selected values
	$type = $params->get('message_type');
	$message = $params->get('user_message');

	//create system message
	echo "<div class=\"$type\">$message</div>";
?>