<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// set attributes
$attributes = array();
$attributes['type']  = 'text';
$attributes['name']  = $name;
$attributes['value'] = $value;
$attributes['class'] = 'title widefat '.(isset($class) ? $class : '');

printf('<input %s />', $this['field']->attributes($attributes, array('label', 'description', 'default')));

?>

<script type="text/javascript">

jQuery(function($){
	
	$('input.title').die('keyup.title').live('keyup.title', function() {
		$(this).trigger('update');
	});

});

</script>