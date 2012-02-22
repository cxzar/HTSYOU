<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppMenu
		Simple menu class.
*/
class AppMenu extends AppTree {

	protected $_name;

	/*
		Function: __construct
			Constructor

		Parameters:
			$name - Menu name

		Returns:
			AppMenu
	*/
	public function __construct($name) {
		parent::__construct();

		$this->_name = $name;
	}

	/*
		Function: render
			Retrieve menu html output

		Returns:
			String
	*/
	public function render() {

		// create html
		$html = '<ul>';
		foreach ($this->_root->getChildren() as $child) {
			$html .= $child->render($this);
		}
		$html .= '</ul>';

		// decorator callbacks ?
		if (func_num_args()) {

			// parse html
			if ($xml = simplexml_load_string($html)) {

				foreach (func_get_args() as $callback) {
					if (is_callable($callback)) {
						$this->_map($xml, $callback);
					}
				}

				$html = $xml->asXML();
			}
		}

		return $html;
	}

	/*
		Function: _map
			Traverses the tree calling the callback on every child.

		Parameters:
			$xml - SimpleXMLElement
			$callback - Callback function
			$args - Callback function arguments

		Returns:
			void
	*/
	protected function _map(SimpleXMLElement $xml, $callback, $args = array()) {

		// init level
		if (!isset($args['level'])) {
			$args['level'] = 0;
		}

		// call function
		call_user_func($callback, $xml, $args);

		// raise level
		$args['level']++;

		// map to all children
		$children = $xml->children();
		if ($n = count($children)) {
			for ($i = 0; $i < $n; $i++) {
				$this->_map($children[$i], $callback, $args);
			}
		}
	}

}

/*
	Class: AppMenuItem
		Simple menu item class.
*/
class AppMenuItem extends AppTreeItem {

	protected $_id;
	protected $_name;
	protected $_link;
	protected $_attributes;

	/*
		Function: __construct
			Constructor

		Parameters:
			$id - Identifier
			$name - Item name
			$link - Item link
			$attributes - Tag attributes

		Returns:
			AppMenuItem
	*/
	public function __construct($id = null, $name = '', $link = null, array $attributes = array()) {
		$this->_id		   = $id;
		$this->_name 	   = $name;
		$this->_link 	   = $link;
		$this->_attributes = $attributes;
	}

	/*
		Function: getName
			Retrieve menu item name

		Returns:
			Mixed
	*/
	public function getName() {
		return $this->_name;
	}

	/*
		Function: setName
			Set a menu item name

		Returns:
			AppMenuItem
	*/
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/*
		Function: getID
			Retrieve menu item identifier

		Returns:
			Mixed
	*/
	public function getID() {
		return $this->_id ? $this->_id : parent::getId();
	}

	/*
		Function: getAttribute
			Retrieve menu item attribute

		Returns:
			Mixed
	*/
	public function getAttribute($key) {

		if (isset($this->_attributes[$key])) {
			return $this->_attributes[$key];
		}

		return null;
	}

	/*
		Function: setAttribute
			Set a menu item attribute

		Returns:
			AppMenuItem
	*/
	public function setAttribute($key, $value) {
		$this->_attributes[$key] = $value;
		return $this;
	}

	/*
		Function: render
			Retrieve menu item html output

		Returns:
			String
	*/
	public function render() {
		$link   = $this->app->request->getVar('hidemainmenu') ? null : $this->_link;
		$html   = array('<li '.JArrayHelper::toString($this->_attributes).'>');
		$html[] = ($link ? '<a href="'.JRoute::_($link).'">' : '<span>').'<span>'.$this->getName().'</span>'.($link ? '</a>' : '</span>');

		if (count($this->getChildren())) {
			$html[] = '<ul>';
			foreach ($this->getChildren() as $child) {
				$html[] = $child->render();
			}
			$html[] = '</ul>';
		}

		$html[] = '</li>';

		return implode("\n", $html);
	}

}

/*
	Class: AppMenuDecorator
		Decorator for menu class.
*/
class AppMenuDecorator {

	/*
		Function: index
			Add item index and level to class attribute

		Parameters:
			$node - XML node
			$args - Callback arguments

		Returns:
			Void
	*/
	public static function index(SimpleXMLElement $node, $args) {

		if ($node->getName() == 'ul') {

			// set ul level
			$level = ($args['level'] / 2) + 1;
			$node->addAttribute('class', trim($node->attributes()->class.' level'.$level));

			// set order/first/last for li
			$count = count($node->children());
			foreach ($node->children() as $i => $child) {
				$child->addAttribute('level', $level);
				$child->addAttribute('order', $i + 1);
				if ($i == 0) $child->addAttribute('first', 1);
				if ($i == $count - 1) $child->addAttribute('last', 1);
			}

		}

		if ($node->getName() == 'li') {

			// level and item order
			$css  = 'level'.$node->attributes()->level;
			$css .= ' item'.$node->attributes()->order;

			// first, last and parent
			if ($node->attributes()->first) $css .= ' first';
			if ($node->attributes()->last)  $css .= ' last';
			if (isset($node->ul))           $css .= ' parent';

			// add li css classes
			$node->attributes()->class = trim($node->attributes()->class.' '.$css);

			// add a/span css classes
			$children = $node->children();
			if ($firstChild = $children[0]) {
				$firstChild->addAttribute('class', trim($firstChild->attributes()->class.' '.$css));
			}
		}

		unset($node->attributes()->level);
		unset($node->attributes()->order);
		unset($node->attributes()->first);
		unset($node->attributes()->last);

	}

}