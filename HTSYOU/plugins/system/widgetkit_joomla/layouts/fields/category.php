<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

printf('<select %s>', $this['field']->attributes(compact('name')));

foreach (JHtml::_('category.options', 'com_content') as $option) {

	// set attributes
	$attributes = array('value' => $option->value);

	// is checked ?
	if ($option->value == $value) {
		$attributes = array_merge($attributes, array('selected' => 'selected'));
	}

	printf('<option %s>%s</option>', $this['field']->attributes($attributes), $option->text);
}

printf('</select>');