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
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

class JElementZooItemorder extends JElement {

	var $_name = 'ZooItemorder';

	function fetchElement($name, $value, &$node, $control_name) {

		// get app
		$app = App::getInstance('zoo');

		// load scripts
		$app->document->addStylesheet('assets:css/ui.css');
		$app->document->addScript('joomla:elements/zooitemorder.js');

		// init vars
		$params     = $app->parameterform->convertParams($this->_parent);
		$item_order = (array) $params->get($name, array('_itemname'));

		$html = array();
		$html[] = '<div id="item-order" class="zoo-itemorder">';

			$html[] = '<div><span class="select-message">'.JText::_('Please select application first').'</span></div>';

			// add types
			$html[] = '<div class="apps">';

			if ($node->attributes('apps') || @$node->attributes()->apps) {
				$applications = $app->application->getApplications();
			} else if ($group = $app->request->getString('group', false)) {
				$applications = array($app->object->create('Application')->setGroup($group));
			} else {
				$applications = array($app->zoo->getApplication());
			}

			foreach ($applications as $application) {

				$types = $application->getTypes();

				// add core elements
				$core = $app->object->create('Type', array('_core', $application));
				$core->name = JText::_('Core');
				array_unshift($types, $core);

				$html[] = '<div class="app '.$application->id.'">';

					foreach ($types as $type) {

						if ($type->identifier == '_core') {
							$elements = $type->getCoreElements();
							$options = array();
						} else {
							$elements = $type->getElements();
							$options = array($app->html->_('select.option', false, '- '.JText::_('Select Element').' -'));
						}

						// filter orderable elements
						$elements = array_filter($elements, create_function('$element', 'return $element->getMetaData("orderable") == "true";'));

						$value = false;
						foreach ($elements as $element) {
							if (in_array($element->identifier, $item_order)) {
								$value = $element->identifier;
							}
							$options[] = $app->html->_('select.option', $element->identifier, ($element->config->name ? $element->config->name : $element->getMetaData('name')));
						}
						if ($type->identifier == '_core' && ($node->attributes('add_default') || @$node->attributes()->add_default)) {
							array_unshift($options, $app->html->_('select.option', '', JText::_('default')));
						}

						$id = $control_name.$name.$application->id.$type->identifier;
						$html[] = '<div class="type">';
						$html[] = $app->html->_('select.genericlist',  $options, "{$control_name}[{$name}][]", 'class="element"', 'value', 'text', $value, $id);
						$html[] = '<label for="'.$id.'">' . $type->name . '</label>';
						$html[] = '</div>';
					}

				$html[] = '</div>';
			}
			$html[] = '</div>';
			$html[] = '<div class="reverse">';
				$html[] = "<input type=\"checkbox\" id=\"{$control_name}[{$name}][_reversed]\" name=\"{$control_name}[{$name}][]\"" . (in_array('_reversed', $item_order) ? 'checked="checked"' : '') . ' value="_reversed" />';
				$html[] = '<label for="'.$id.'">' . JText::_('Reverse') . '</label>';
			$html[] = '</div>';

			if ($node->attributes('random') || @$node->attributes()->random) {
				$html[] = '<div class="random">';
					$html[] = "<input type=\"checkbox\" id=\"{$control_name}[{$name}][_random]\" name=\"{$control_name}[{$name}][]\"" . (in_array('_random', $item_order) ? 'checked="checked"' : '') . ' value="_random" />';
					$html[] = '<label for="'.$id.'">' . JText::_('Random') . '</label>';
				$html[] = '</div>';
			}

		$html[] = '</div>';

		$javascript  = "jQuery('#item-order').ZooItemOrder();";
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return implode("\n", $html).$javascript;
	}

}