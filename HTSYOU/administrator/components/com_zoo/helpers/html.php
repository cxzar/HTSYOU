<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: HTMLHelper
   	  A class that contains zoo html functions
*/
class HTMLHelper extends AppHelper {

	public function _($type) {

		// get arguments
		$args = func_get_args();

		// Check to see if we need to load a helper file
		$parts = explode('.', $type);

		if (count($parts) >= 2) {
			$func = array_pop($parts);
			$file = array_pop($parts);

			if (in_array($file, array('zoo', 'control')) && method_exists($this, $func)) {
				array_shift($args);
				return call_user_func_array(array($this, $func), $args);
			}
		}

		return call_user_func_array(array('JHTML', '_'), $args);

	}

	/*
    	Function: calendar
    	  Get zoo datepicker.

	   Returns:
	      Returns zoo datepicker html string.
 	*/
	public function calendar($value, $name, $id, $attribs = null, $time = false)	{

		if (!defined('ZOO_CALENDAR_SCRIPT_DECLARATION')) {
			define('ZOO_CALENDAR_SCRIPT_DECLARATION', true);

			$this->app->document->addScript('assets:js/date.js');

			$translations = array(
				'closeText' => 'Done',
				'currentText' => 'Today',
				'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
				'dayNamesMin' => array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
				'dayNamesShort' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
				'monthNames' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
				'monthNamesShort' => array('JANUARY_SHORT', 'FEBRUARY_SHORT', 'MARCH_SHORT', 'APRIL_SHORT', 'MAY_SHORT',
					'JUNE_SHORT', 'JULY_SHORT', 'AUGUST_SHORT', 'SEPTEMBER_SHORT', 'OCTOBER_SHORT', 'NOVEMBER_SHORT', 'DECEMBER_SHORT'),
				'prevText' => 'Prev',
				'nextText' => 'Next',
				'weekHeader' => 'Wk',
				'appendText' => '(yyyy-mm-dd)'
			);

			$timepicker_translations = array(
				'currentText' => 'Now',
				'closeText' => 'Done',
				'timeOnlyTitle' => 'Choose Time',
				'timeText' => 'Time',
				'hourText' => 'Hour',
				'minuteText' => 'Minute',
				'secondText' => 'Second'
			);

			foreach ($translations as $key => $translation) {
				$translations[$key] = is_array($translation) ? array_map(array('JText', '_'), $translation) : JText::_($translation);
			}
			$timepicker_translations = array_map(array('JText', '_'), $timepicker_translations);

			$javascript = 'jQuery(function($) { $("body").Calendar({ translations: '.json_encode($translations).', timepicker_translations: '.json_encode($timepicker_translations).' });  });';

			$this->app->document->addScriptDeclaration($javascript);

		}

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$rel = '';
		if ($time) {
			$rel = ' rel="timepicker"';
		}

		return '<input'.$rel.' style="width: 110px" type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'
			.'<img src="'.JURI::root(true).'/templates/system/images/calendar.png'.'" class="zoo-calendar" />';
	}

	/*
    	Function: image
    	  Get image resource info.

	   Returns:
	      Array - Image info
 	*/
	public function image($image, $width = null, $height = null) {

		$resized_image = $this->app->zoo->resizeImage(JPATH_ROOT.DS.$image, $width, $height);
		$inner_path    = $this->app->path->relative($resized_image);
		$path 		   = JPATH_ROOT.'/'.$inner_path;

		if (is_file($path) && $size = getimagesize($path)) {

			$info['path'] 	= $path;
			$info['src'] 	= JURI::root().$inner_path;
			$info['mime'] 	= $size['mime'];
			$info['width'] 	= $size[0];
			$info['height'] = $size[1];
			$info['width_height'] = sprintf('width="%d" height="%d"', $info['width'], $info['height']);

			return $info;
		}

		return null;
	}

	/*
		Function: categoryList
			Returns category select list html string.
	*/
	public function categoryList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false, $category = 0) {

		// set options
		settype($options, 'array');
		reset($options);

		// get category tree list
		$list = $this->app->tree->buildList($category, $application->getCategoryTree(), array(), '-&nbsp;', '.&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;');

		// create options
		foreach ($list as $category) {
			$options[] = $this->_('select.option', $category->id, $category->treename);
		}

		return $this->_('zoo.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);

	}

 	/*
    	Function: editRow
    		Returns edit row html string.
 	*/
	public function editRow($name, $value) {

		$html  = "\t<tr>\n";
		$html .= "\t\t<td style=\"color:#666666;\">$name</td>\n";
		$html .= "\t\t<td>$value</td>\n";
		$html .= "\t</tr>\n";

		return $html;
	}

	public function countrySelectList($countries, $name, $selected, $multiselect) {

        $options = array ();
        if (!$multiselect) {
			$options[] = $this->app->html->_('select.option', '', '-' . JText::_('Select Country') . '-');
        }

        foreach ($countries as $key => $country) {
			$options[] = $this->app->html->_('select.option', $key, JText::_($country));
        }

        $attribs = $multiselect ? 'size="'.max(min(count($options), 10), 3).'" multiple="multiple"' : '';

        return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/*
		Function: layoutList
			Returns layout select list html string.
	*/
	public function layoutList($application, $type_id, $layout_type, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

        $layouts = $this->app->zoo->getLayouts($application, $type_id, $layout_type);

        foreach ($layouts as $layout => $metadata) {
            $options[] = $this->_('select.option', $layout, $metadata->get('name'));
        }

        return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);

	}

 	/*
    	Function: typeList
    		Returns type select list html string.
 	*/
	public function typeList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $filter = array()) {

		// set options
		settype($options, 'array');
		reset($options);

		foreach ($application->getTypes() as $type) {
			if (empty($filter) || in_array($type->id, $filter)) {
				$options[] = $this->_('select.option', $type->id, JText::_($type->name));
			}
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: moduleList
    		Returns module select list html string.
 	*/
	public function moduleList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		// get modules
		$modules = $this->app->module->load();

		if (count($modules)) {

			foreach ($modules as $module) {
				$options[] = $this->app->html->_('select.option', $module->id, $module->title.' ('.$module->position.')');
			}

			return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
		}

		return JText::_("There are no modules to choose from.");
	}

 	/*
    	Function: authorList
    		Returns author select list html string.
 	*/
    public function authorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $show_registered_users = true) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
			    .' FROM #__users AS u'
                . ' WHERE u.block = 0'
                . ($show_registered_users ? '' : ' AND u.gid > 18');

		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
    }

 	/*
    	Function: itemAuthorList
    		Returns author select list html string.
 	*/
	public function itemAuthorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
			    .' FROM '.ZOO_TABLE_ITEM.' AS i'
			    .' JOIN #__users AS u ON i.created_by = u.id';

		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: itemList
    		Returns item select list html string.
 	*/
	public function itemList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$query = 'SELECT DISTINCT c.item_id as value, a.name as text'
			   	.' FROM '.ZOO_TABLE_COMMENT.' AS c'
			   	.' LEFT JOIN '.ZOO_TABLE_ITEM. ' AS a ON c.item_id = a.id'
			   	.' WHERE a.application_id = ' . (int) $application->id
			   	.' ORDER BY a.name';

		$rows = $this->app->database->queryAssocList($query);

		foreach ($rows as $row) {
			$options[] = $this->_('select.option', $row['value'], $row['text']);
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

  	/*
    	Function: accessLevel
    		Returns user access select list.
 	*/
	public function accessLevel($options, $name, $attribs = 'class="inputbox"', $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $exclude = array()) {

		// set options
		settype($options, 'array');
		reset($options);

		// set exclude
		$exclude = (array) $exclude;
		reset($exclude);

		$groups = $this->app->zoo->getGroups();
        foreach ($exclude as $key) {
            unset($groups[$key]);
        }
		foreach ($groups as $group) {
			$options[] = $this->_('select.option', $group->id, JText::_($group->name), $key, $text);
		}

		if (!isset($groups[$selected])) {
			$selected = $this->app->joomla->getDefaultAccess();
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);

	}

 	/*
    	Function: itemAuthorList
    		Returns author select list html string.
 	*/
	public function commentAuthorList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = "SELECT DISTINCT c.author AS value, c.author AS text"
			    ." FROM ".ZOO_TABLE_COMMENT." AS c"
			    .' LEFT JOIN '.ZOO_TABLE_ITEM. ' AS a ON c.item_id = a.id'
				." WHERE c.author <> ''"
				." AND a.application_id = " . $application->id
				." ORDER BY c.author";
		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: queryList
			Returns select list html string.
 	*/
	public function queryList($query, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$options = array_merge($options, $this->app->database->queryObjectList($query));
		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: arrayList
			Returns select list html string.
 	*/
	public function arrayList($array, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$options = array_merge($options, $this->listOptions($array));
		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: selectOptions
    		Returns select option as JHTML compatible array.
 	*/
	public function listOptions($array, $value = 'value', $text = 'text') {

		$options = array();

		if (is_array($array)) {
			foreach ($array as $val => $txt) {
				$options[] = $this->_('select.option', strval($val), $txt, $value, $text);
			}
		}

		return $options;
	}

 	/*
    	Function: genericList
    		Wrapper for Joomla 1.5/1.6
 	*/
	public function genericList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false) {
		if ($this->app->joomla->isVersion('1.5')) {
			return $this->app->html->_('select.genericlist', $data, $name, $attribs, $optKey, $optText, $selected);
		} else {
			$attributes['list.attr'] = $attribs;
			$attributes['id'] = $idtag;
			$attributes['list.translate'] = $translate;
			$attributes['option.key'] = $optKey;
			$attributes['option.text'] = $optText;
			$attributes['list.select'] = $selected;
			$attributes['option.text.toHtml'] = false;

			return $this->app->html->_('select.genericlist', $data, $name, $attributes);
		}
	}


	//******************************
	//* ZOO Controls			   *
	//******************************

	/*
    	Function: selectdirectory
    		Returns directory select html string.
 	*/
	function selectdirectory($directory, $filter, $name, $value = null, $attribs = null) {

		// get directories
		$options = array($this->app->html->_('select.option',  '', '- '.JText::_('Select Directory').' -'));
		$dirs    = $this->app->path->dirs($directory, true, $filter);

		natsort($dirs);

		foreach ($dirs as $dir) {
			$options[] = $this->app->html->_('select.option', $dir, $dir);
		}

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $value);
	}

	/*
    	Function: textarea
    		Returns form textarea html string.
 	*/
	function textarea($name, $value = null, $attribs = null) {

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return "\n\t<textarea name=\"$name\" $attribs />".$value."</textarea>\n";

	}

 	/*
    	Function: text
    		Returns form text input html string.
 	*/
	function text($name, $value = null, $attribs = null) {
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		return $this->app->html->_('control.input', 'text', $name, $value, $attribs);
	}

 	/*
    	Function: input
    		Returns form input html string.
 	*/
	function input($type, $name, $value = null, $attribs = null) {

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		return "\n\t<input type=\"$type\" name=\"$name\" value=\"$value\" $attribs />\n";

	}

}