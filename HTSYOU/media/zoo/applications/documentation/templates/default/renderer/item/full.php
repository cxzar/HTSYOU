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

<?php if ($this->checkPosition('title')) : ?>
<h1 class="pos-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php if ($this->checkPosition('content')) : ?>
<div class="pos-content">
	<?php echo $this->renderPosition('content', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('related')) : ?>
<div class="pos-related">
	<h3><?php echo JText::_('Related Links'); ?></h3>
	<ul>
		<?php echo $this->renderPosition('related'); ?>
	</ul>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('meta') || $this->checkPosition('taxonomy')) : ?>
<div class="meta">
	
	<?php if ($this->checkPosition('meta')) : ?>
	<ul class="pos-meta">
		<?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
	</ul>
	<?php endif; ?>
	
	<?php if ($this->checkPosition('taxonomy')) : ?>
	<ul class="pos-taxonomy">
		<?php echo $this->renderPosition('taxonomy', array('style' => 'list')); ?>
	</ul>
	<?php endif; ?>
	
</div>
<?php endif; ?>

<?php if ($this->checkPosition('bottom')) : ?>
<div class="pos-bottom">
	<?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
</div>
<?php endif; ?>