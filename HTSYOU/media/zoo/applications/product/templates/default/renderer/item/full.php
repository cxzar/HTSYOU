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

	<div class="box-t1">
		<div class="box-t2">
			<div class="box-t3"></div>
		</div>
	</div>
	
	<div class="box-1">
		<?php echo $this->renderPosition('top', array('style' => 'block')); ?>
	</div>
	
	<div class="box-b1">
		<div class="box-b2">
			<div class="box-b3"></div>
		</div>
	</div>

</div>
<?php endif; ?>

<div class="floatbox">

	<div class="box-t1">
		<div class="box-t2">
			<div class="box-t3"></div>
		</div>
	</div>
	
	<div class="box-1">

		<?php if ($this->checkPosition('media')) : ?>
		<div class="pos-media <?php echo 'media-'.$view->params->get('template.item_media_alignment'); ?>">
			<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>
	
		<?php if ($this->checkPosition('title')) : ?>
		<h1 class="pos-title"><?php echo $this->renderPosition('title'); ?></h1>
		<?php endif; ?>
	
		<?php if ($this->checkPosition('description')) : ?>
		<div class="pos-description">
			<?php echo $this->renderPosition('description', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>
	
		<?php if ($this->checkPosition('specification')) : ?>
		<div class="pos-specification">
			<h3><?php echo JText::_('Specifications'); ?></h3>
			<ul>
				<?php echo $this->renderPosition('specification', array('style' => 'list')); ?>
			</ul>
		</div>
		<?php endif; ?>
	
		<?php if ($this->checkPosition('bottom')) : ?>
		<div class="pos-bottom">
			<?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>
		
		<?php if ($this->checkPosition('related')) : ?>
		<div class="pos-related">
			<?php echo $this->renderPosition('related', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>
	
	</div>
	
	<div class="box-b1">
		<div class="box-b2">
			<div class="box-b3"></div>
		</div>
	</div>
	
</div>