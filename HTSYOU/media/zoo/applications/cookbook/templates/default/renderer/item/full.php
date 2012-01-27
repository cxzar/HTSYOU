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

<?php if ($this->checkPosition('top')) : ?>
<div class="pos-top">
	<?php echo $this->renderPosition('top', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('title') && $this->checkPosition('header')) : ?>
<div class="pos-header">

	<?php if ($this->checkPosition('title')) : ?>
	<h1 class="pos-title"><?php echo $this->renderPosition('title'); ?></h1>
	<?php endif; ?>

	<?php if ($this->checkPosition('header')) : ?>
	<?php echo $this->renderPosition('header', array('style' => 'block')); ?>
	<?php endif; ?>

</div>
<?php endif; ?>

<?php if ($this->checkPosition('infobar')) : ?>
<ul class="pos-infobar">
	<?php echo $this->renderPosition('infobar', array('style' => 'list')); ?>
</ul>
<?php endif; ?>

<?php if ($this->checkPosition('media') || $this->checkPosition('ingredients')) : ?>
<div class="ingredients">

	<?php if ($this->checkPosition('media')) : ?>
	<div class="pos-media <?php echo 'media-'.$view->params->get('template.item_media_alignment'); ?>">
		<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('ingredients')) : ?>
	<div class="pos-ingredients">
		<?php echo $this->renderPosition('ingredients', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

</div>
<?php endif; ?>

<?php if ($this->checkPosition('sidebar') || $this->checkPosition('directions')) : ?>
<div class="directions">

	<?php if ($this->checkPosition('sidebar')) : ?>
	<div class="pos-sidebar <?php echo 'sidebar-'.$view->params->get('template.item_sidebar_alignment'); ?>">
		<?php echo $this->renderPosition('sidebar', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('directions')) : ?>
	<div class="pos-directions">
		<?php echo $this->renderPosition('directions', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

</div>
<?php endif; ?>

<?php if ($this->checkPosition('bottom')) : ?>
<div class="pos-bottom">
	<?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
</div>
<?php endif; ?>