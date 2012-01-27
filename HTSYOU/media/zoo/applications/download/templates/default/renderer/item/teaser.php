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

<?php if ($this->checkPosition('media')) : ?>
<div class="pos-media <?php echo 'media-'.$view->params->get('template.items_media_alignment'); ?>">
	<?php echo $this->renderPosition('media'); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
	<h2 class="pos-title"><?php echo $this->renderPosition('title'); ?></h2>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<p class="pos-meta">
	<?php echo $this->renderPosition('meta'); ?>
</p>
<?php endif; ?>

<?php if ($this->checkPosition('specification')) : ?>
<ul class="pos-specification">
	<?php echo $this->renderPosition('specification', array('style' => 'list')); ?>
</ul>
<?php endif; ?>

<?php if ($this->checkPosition('button')) : ?>
<div class="pos-button">
	<?php echo $this->renderPosition('button'); ?>
</div>
<?php endif; ?>