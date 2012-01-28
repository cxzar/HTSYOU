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
<div class="sub-pos-media media-left">
	<?php echo $this->renderPosition('media'); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h4 class="sub-pos-title">
	<?php echo $this->renderPosition('title'); ?>
</h4>
<?php endif; ?>

<?php if ($this->checkPosition('description')) : ?>
<div class="sub-pos-description">
	<?php echo $this->renderPosition('description', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('specification')) : ?>
<ul class="sub-pos-specification">
	<?php echo $this->renderPosition('specification', array('style' => 'list')); ?>
</ul>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
<p class="sub-pos-links">
	<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
</p>
<?php endif; ?>