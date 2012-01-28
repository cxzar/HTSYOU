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

<div class="items">

	
	<h1 class="title">
		<?php if ($title) echo $title.' '.JText::_('Files'); ?>
		<?php if ($subtitle) : ?>
			<span class="subtitle"> - <?php echo $subtitle; ?></span>
		<?php endif; ?>
	</h1>
	

	<?php

		// init vars
		$i = 0;
		$columns = $this->params->get('template.items_cols', 2);		
		
		// render rows
		foreach($this->items as $item) {
			if ($i % $columns == 0) echo ($i > 0 ? '</div><div class="row">' : '<div class="row first-row">');
			$firstcell = ($i % $columns == 0) ? 'first-cell' : null;
			echo '<div class="width'.intval(100 / $columns).' '.$firstcell.'">'.$this->partial('item', compact('item')).'</div>';
			$i++;
		}
		echo '</div>';
	
	?>

	<?php echo $this->partial('pagination'); ?>

</div>