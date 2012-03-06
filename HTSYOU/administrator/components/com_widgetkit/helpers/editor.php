<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

jimport('joomla.html.editor');

/*
	Class: EditorWidgetkitHelper
		Editor helper class, to integrate the Joomla Editor Plugins.
*/
class EditorWidgetkitHelper extends WidgetkitHelper {

	/*
		Function: init
			Init System Editor
			Mixed
	*/
	public function init() {
		
		if (is_a($this['system']->document ,'JDocumentRAW')) {
			return;
		}
		
		$editor = JFactory::getConfig()->getValue('config.editor');

		if (in_array(strtolower($editor), array('tinymce', 'jce'))) {
			JEditorWidgetkit::getInstance($editor)->_loadEditor();
		}
	}

}

/*
	Class: JEditorWidgetkit
		Custom editor class. Just to have _loadEditor() as public method
*/
class JEditorWidgetkit extends JEditor {

	/*
		Function: init
			Returns the global Editor object, only creating it if it doesn't already exist.
		
		Parameters:
			String $editor - The editor to use.
		
		Returns:
			JEditorWidgetkit Obj

	*/
	public static function getInstance($editor = 'none') {
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$signature = serialize($editor);

		if (empty ($instances[$signature])) {
			$instances[$signature] = new JEditorWidgetkit($editor);
		}

		return $instances[$signature];
	}

	/*
		Function: _loadEditor
			Load the editor
			
		Parameters:
			Array $config - Associative array of editor config paramaters.
		
		Returns:
			Mixed
	*/
	public function _loadEditor($config = array()) {
		return parent::_loadEditor($config);
	}

}