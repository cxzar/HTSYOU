<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ItemOrderHelper
   The Helper Class for item order
*/
class ItemOrderHelper extends AppHelper {

	/*
		Function: convert
			Translate pre ZOO 2.5 item order to new item order.

		Parameters:
			$order - the order string

		Returns:
			Mixed - New Item Order as array, or unchanged
	*/
	public function convert($order) {
		$orderings = array(
			'date'   => array('_itemcreated'),
			'rdate'  => array('_itemcreated', '_reversed'),
			'alpha'  => array('_itemname'),
			'ralpha' => array('_itemname', '_reversed'),
			'hits'   => array('_itemhits'),
			'rhits'  => array('_itemhits', '_reversed'),
			'random' => array('_random'));
		return isset($orderings[$order]) ? $orderings[$order] : $order;
	}

}