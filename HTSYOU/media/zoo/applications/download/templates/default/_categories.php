<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="categories">

	<?php

		// init vars
		$i = 0;
		
		// render rows
		foreach($this->selected_categories as $category) {
			if ($category && !$category->totalItemCount()) continue;
			if ($i % $this->params->get('template.categories_cols') == 0) echo ($i > 0 ? '</div><div class="row">' : '<div class="row first-row">');
			$firstcell = ($i % $this->params->get('template.categories_cols') == 0) ? 'first-cell' : null;
			echo '<div class="width'.intval(100 / $this->params->get('template.categories_cols')).' '.$firstcell.'">'.$this->partial('category', compact('category')).'</div>';
			$i++;
		}
		echo '</div>';
	
	?>

</div>