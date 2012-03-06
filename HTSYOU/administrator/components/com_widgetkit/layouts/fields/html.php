<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// set attributes
$attributes = array();
$attributes['id']    = 'html-editor-'.uniqid();
$attributes['class'] = 'html-editor';
$attributes['name']  = $name;

printf('<textarea %s>%s</textarea>', $this['field']->attributes($attributes), $value);

?>

<script type="text/javascript">

	jQuery(function($){
		
		var id = '<?php echo $attributes["id"]; ?>';
		var editor = window['WFEditor'] || window['JContentEditor'] || window['tinyMCE'];
		
		if (!editor || $('#' + id + '_tbl').length) {
			return;
		}

		if(window['WFEditor']){
			$('#' + id).after('<input type="hidden" id="wf_'+id+'_token" value="'+Math.random()+'">');
		}

		new tinymce.Editor(id, $.extend(editor.settings, {'forced_root_block': ''})).render();

		$('#' + id).bind({
			'editor-action-start': function() {
				tinyMCE.execCommand('mceRemoveControl', false, id);
			},
			'editor-action-stop': function() {
				tinyMCE.execCommand('mceAddControl', true, id);
			}
		});

	});

</script>