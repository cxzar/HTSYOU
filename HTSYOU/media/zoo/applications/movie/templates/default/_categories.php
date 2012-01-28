<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// remove empty categories
$selected_categories = array();
foreach($this->selected_categories as $category) {
	if ($category->totalItemCount()) {
		$selected_categories[] = $category;
	}
}

// init vars
$i       = 0;
$columns = array();
$column  = 0;
$row     = 0;
$rows    = ceil(count($selected_categories) / $this->params->get('template.categories_cols'));

// create columns
foreach($selected_categories as $category) {

	if ($this->params->get('template.categories_order')) {
		// order down
		if ($row >= $rows) {
			$column++;
			$row  = 0;
			$rows = ceil((count($selected_categories) - $i) / ($this->params->get('template.categories_cols') - $column));
		}
		$row++;
		$i++;
	} else {
		// order across
		$column = $i++ % $this->params->get('template.categories_cols');
	}

	if (!isset($columns[$column])) {
		$columns[$column] = '';
	}

	$columns[$column] .= $this->partial('category', compact('category'));
}

// render columns
$count = count($columns);
if ($count) {
	echo '<div class="categories categories-col-'.$count.'">';
	for ($j = 0; $j < $count; $j++) {
		$first = ($j == 0) ? ' first' : null;
		$last  = ($j == $count - 1) ? ' last' : null;
		echo '<div class="width'.intval(100 / $count).$first.$last.'">'.$columns[$j].'</div>';
	}
	echo '</div>';
}