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

<div class="items <?php if ($has_categories) echo 'items-separator'; ?>">

	<?php if ($itemstitle) : ?>
	<h2 class="items-title"><?php echo $itemstitle; ?></h2>
	<?php endif; ?>

	<?php
			
		// init vars
		$i = 0;
		$columns = $this->params->get('template.items_cols', 2);
		reset($this->items);
		
		// render rows
		while((list($key, $item) = each($this->items)) || ($i % $columns != 0)) {
			if ($i % $columns == 0) echo ($i > 0 ? '</div><div class="row">' : '<div class="row first-row">');
			$first = ($i % $columns == 0) ? ' first-item' : null;
			echo '<div class="width'.intval(100 / $columns).$first.'">'.$this->partial('item', compact('item')).'</div>';
			$i++;
		}
		echo '</div>';		

	?>
	
	<?php echo $this->partial('pagination'); ?>

</div>