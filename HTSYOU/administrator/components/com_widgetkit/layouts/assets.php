<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get stylesheets/javascripts
$styles  = $this['asset']->get('css');
$scripts = $this['asset']->get('js');

// cache stylesheets/javascripts, if cache is writable
if (is_writable($this['path']->path('cache:'))) {

	$styles = array($this['asset']->cache('widgetkit.css', $this['asset']->get('css'), array('CSSImportResolver', 'CSSRewriteURL', 'CSSCompressor')));
	foreach ($styles[0] as $style) {
		if ($style->getType() == 'File' && !$style->getPath()) {
			$styles[] = $style;
		}
	}

	$scripts = array($this['asset']->cache('widgetkit.js', $this['asset']->get('js'), array('JSCompressor')));
	foreach ($scripts[0] as $script) {
		if ($script->getType() == 'File' && !$script->getPath()) {
			$scripts[] = $script;
		}
	}
	
}

// load jQuery, if not loaded before
if (!$this['system']->application->get('jquery')) {
	$this['system']->application->set('jquery', true);
	$this['system']->document->addScript($this['path']->url('widgetkit:js/jquery.js'));
}

// add styles
foreach ($styles as $style) {
	if ($url = $style->getUrl()) {
		$this['system']->document->addStyleSheet($url);
	} else {
		$this['system']->document->addStyleDeclaration($style->getContent());
	}
}

// add scripts
foreach ($scripts as $script) {
	if ($url = $script->getUrl()) {
		$this['system']->document->addScript($url);
	} else {
		$this['system']->document->addScriptDeclaration($script->getContent());
	}
}

// internet explorer
if ($this['useragent']->browser() == 'msie') {
	$this['system']->document->addCustomTag(sprintf("<!--[if lte IE 8]><link rel=\"stylesheet\" href=\"%s\" /><![endif]-->\n", $this['path']->url('widgetkit:css/ie.css')));
}