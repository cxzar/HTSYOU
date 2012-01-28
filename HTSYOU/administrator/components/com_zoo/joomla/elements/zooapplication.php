<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementZooApplication extends JElement {

	var	$_name = 'ZooApplication';

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		$app = App::getInstance('zoo');

		// load zoo frontend language file
		$app->system->language->load('com_zoo');

		$app->html->_('behavior.modal', 'a.modal');
		$app->document->addStylesheet('joomla:elements/zooapplication.css');
		$app->document->addScript('joomla:elements/zooapplication.js');

		// init vars
		$params		= $app->parameterform->convertParams($this->_parent);
		$table		= $app->table->application;
		$attributes = $node instanceof JSimpleXMLElement ? $node->_attributes : $node->attributes();

		// set modes
		$modes = array();

		if ($node->attributes('categories') || @$node->attributes()->categories) {
			$modes[] = $app->html->_('select.option', 'categories', JText::_('Categories'));
		}

		if ($node->attributes('types') || @$node->attributes()->types) {
			$modes[] = $app->html->_('select.option', 'types', JText::_('Types'));
		}

		if ($node->attributes('items') || @$node->attributes()->items) {
			$modes[] = $app->html->_('select.option', 'item', JText::_('Item'));
		}

		// create application/category select
		$cats    = array();
		$types   = array();
		$options = array($app->html->_('select.option', '', '- '.JText::_('Select Application').' -'));

		foreach ($table->all(array('order' => 'name')) as $application) {

			// application option
			$options[] = $app->html->_('select.option', $application->id, $application->name);

			// create category select
			if ($node->attributes('categories') || @$node->attributes()->categories) {
				$attribs = 'class="category app-'.$application->id.($value != $application->id ? ' hidden' : null).'" data-category="'.$control_name.'[category]"';
				$opts    = $node->attributes('frontpage') || @$node->attributes()->frontpage ? array($app->html->_('select.option', '', '&#8226;	'.JText::_('Frontpage'))) : array();
				$cats[]  = $app->html->_('zoo.categorylist', $application, $opts, ($value == $application->id ? $control_name.'[category]' : null), $attribs, 'value', 'text', $params->get('category'));
			}

			// create types select
			if ($node->attributes('types') || @$node->attributes()->types) {
				$opts = array();

				foreach ($application->getTypes() as $type) {
					$opts[] = $app->html->_('select.option', $type->id, $type->name);
				}

				$attribs = 'class="type app-'.$application->id.($value != $application->id ? ' hidden' : null).'" data-type="'.$control_name.'[type]"';
				$types[] = $app->html->_('select.genericlist', $opts, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'));
			}
		}

		// create html
		$html[] = '<div id="'.$name.'" class="zoo-application">';
		$html[] = $app->html->_('select.genericlist', $options, $control_name.'['.$name.']', 'class="application"', 'value', 'text', $value);

		// create mode select
		if (count($modes) > 1) {
			$html[] = $app->html->_('select.genericlist', $modes, $control_name.'[mode]', 'class="mode"', 'value', 'text', $params->get('mode'));
		}

		// create categories html
		if (!empty($cats)) {
			$html[] = '<div class="categories">'.implode("\n", $cats).'</div>';
		}

		// create types html
		if (!empty($types)) {
			$html[] = '<div class="types">'.implode("\n", $types).'</div>';
		}

		// create items html
		$link = '';
		if ($node->attributes('items') || @$node->attributes()->items) {

			$field_name	= $control_name.'[item_id]';
			$item_name  = JText::_('Select Item');

			if ($item_id = $params->get('item_id')) {
				$item = $app->table->item->get($item_id);
				$item_name = $item->name;
			}

			$link = $app->link(array('controller' => 'item', 'task' => 'element', 'tmpl' => 'component', 'func' => 'selectZooItem', 'object' => $name), false);

			$html[] = '<div class="item">';
			$html[] = '<input type="text" id="'.$name.'_name" value="'.htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />';
			$html[] = '<a class="modal" title="'.JText::_('Select Item').'"  href="#" rel="{handler: \'iframe\', size: {x: 850, y: 500}}">'.JText::_('Select').'</a>';
			$html[] = '<input type="hidden" id="'.$name.'_id" name="'.$field_name.'" value="'.(int)$item_id.'" />';
			$html[] = '</div>';

		}

		$html[] = '</div>';

		$javascript  = 'jQuery(function($) { jQuery("#'.$name.'").ZooApplication({ url: "'.$link.'", msgSelectItem: "'.JText::_('Select Item').'" }); });';
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return implode("\n", $html).$javascript;
	}

}