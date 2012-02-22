<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class JElementZooList extends JElement {

	var	$_name = 'ZooList';

	function fetchElement($name, $value, &$node, $control_name)	{

		$id = $control_name.$name;
		$name = $control_name.'['.$name.']';
		$class = ($node->attributes('class') ? $node->attributes('class') : 'inputbox');

		$html[] = sprintf('<select %s>', $this->attributes(compact('name', 'class', 'id')));

		foreach ($node->children() as $option) {

			// set attributes
			$attributes = array('value' => $option->data());

			// is checked ?
			if ($option->data() == $value) {
				$attributes = array_merge($attributes, array('selected' => 'selected'));
			}

			$html[] = sprintf('<option %s>%s</option>', $this->attributes($attributes), $option->attributes('name'));
		}

		$html[] = sprintf('</select>');

		return implode("\n", $html);

	}

	public function attributes($attributes, $ignore = array()) {

		$attribs = array();
		$ignore  = (array) $ignore;

		foreach ($attributes as $name => $value) {
			if (in_array($name, $ignore)) continue;

			$attribs[] = sprintf('%s="%s"', $name, htmlspecialchars($value));
		}

		return implode(' ', $attribs);
	}

}
