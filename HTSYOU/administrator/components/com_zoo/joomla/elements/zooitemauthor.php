<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementZooItemAuthor extends JElement {

	var	$_name = 'ZooItemAuthor';

	function fetchElement($name, $value, &$node, $control_name)	{

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		if ($app->joomla->isVersion('1.5')) {

			$options[] = $app->html->_('select.option',  'NO_CHANGE', '- '. JText::_( 'No Change' ) .' -' );

			return $app->html->_('zoo.authorList', $options, $control_name.'['.$name.']',  null, 'value', 'text', $value, false, false, $show_registered_users = false);

		} else {

			// Initialize variables.
			$html = array();
			$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field='.$name;

			// Initialize some field attributes.
			$attr = $node->attributes('class') ? ' class="'.(string) $node->attributes('class').'"' : '';
			$attr .= $node->attributes('size') ? ' size="'.(int) $node->attributes('size').'"' : '';

			// Initialize JavaScript field attributes.
			$onchange = (string) $node->attributes('onchange');

			// Load the modal behavior script.
			$app->html->_('behavior.modal', 'a.modal_'.$name);

			// Build the script.
			$script = array();
			$script[] = '	function jSelectUser_'.$name.'(id, title) {';
			$script[] = '		var old_id = document.getElementById("'.$name.'_id").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("'.$name.'_id").value = id;';
			$script[] = '			document.getElementById("'.$name.'_name").value = title;';
			$script[] = '			'.$onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Load the current username if available.
			$username = $value == 'NO_CHANGE' ? JText::_( 'No Change' ) : (($user = $app->user->get($value)) && $user->id ? $user->name : JText::_('JLIB_FORM_SELECT_USER'));

			// Create a dummy text field with the user name.
			$html[] = '<div class="fltlft">';
			$html[] = '	<input type="text" id="'.$name.'_name"' .
						' value="'.htmlspecialchars($username, ENT_COMPAT, 'UTF-8').'"' .
						' disabled="disabled"'.$attr.' />';
			$html[] = '</div>';

			// Create the user select button.
			$html[] = '<div class="button2-left">';
			$html[] = '  <div class="blank">';
			if ($node->attributes('readonly') != 'true') {
				$html[] = '		<a class="modal_'.$name.'" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"' .
								' href="'.$link.'"' .
								' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
				$html[] = '			'.JText::_('JLIB_FORM_CHANGE_USER').'</a>';
			}
			$html[] = '  </div>';
			$html[] = '</div>';

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="'.$name.'_id" name="'.$name.'" value="'.(int) $value.'" />';

			return implode("\n", $html);

		}
	}
}