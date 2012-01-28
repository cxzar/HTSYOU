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

<div class="teaser-item">
<?php if ($item) : ?>
	
	<?php echo $this->renderer->render('item.teaser', array('view' => $this, 'item' => $item)); ?>
	
<?php endif; ?>
</div>