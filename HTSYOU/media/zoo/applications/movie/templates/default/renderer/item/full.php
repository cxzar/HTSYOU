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

<div class="floatbox">

	<?php if ($this->checkPosition('sidebar')) : ?>
	<div class="pos-sidebar <?php echo 'sidebar-'.$view->params->get('template.item_sidebar_alignment'); ?>">
		<?php echo $this->renderPosition('sidebar', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('title')) : ?>
	<h1 class="pos-title"><?php echo $this->renderPosition('title'); ?></h1>
	<?php endif; ?>

	<?php if ($this->checkPosition('meta')) : ?>
	<p class="pos-meta">
		<?php echo $this->renderPosition('meta', array('style' => 'comma')); ?>
	</p>
	<?php endif; ?>

	<?php if ($this->checkPosition('description')) : ?>
	<div class="pos-description">
		<?php echo $this->renderPosition('description', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('specification')) : ?>
	<ul class="pos-specification">
		<?php echo $this->renderPosition('specification', array('style' => 'list')); ?>
	</ul>
	<?php endif; ?>

	<?php if ($view->params->get('template.show_spoiler_warning')) : ?>
	<p class="spoiler"><?php echo '<span class="warning">'.JText::_('Warning').'</span> '.JText::_('The following summary contains spoilers'); ?></p>
	<?php endif; ?>
	
	<?php if ($this->checkPosition('summary')) : ?>
	<div class="pos-summary">
		<?php echo $this->renderPosition('summary', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>
	
</div>

<?php if ($this->checkPosition('bottom')) : ?>
<div class="pos-bottom">
	<?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
</div>
<?php endif; ?>