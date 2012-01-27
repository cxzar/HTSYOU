<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$params = $item->getParams('site');


/* set media alignment */
$align = ($this->checkPosition('media')) ? $params->get('template.teaseritem_media_alignment') : '';

?>

<?php if ($align == "above") : ?>
<div class="pos-media media-top"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('title') || $this->checkPosition('meta')) : ?>
<header>

	<?php if ($this->checkPosition('title')) : ?>
	<h1 class="title"><?php echo $this->renderPosition('title'); ?></h1>
	<?php endif; ?>
	
	<?php if ($this->checkPosition('meta')) : ?>
	<p class="meta"><?php echo $this->renderPosition('meta'); ?></p>
	<?php endif; ?>

</header>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<p class="pos-subtitle"><?php echo $this->renderPosition('subtitle'); ?></p>
<?php endif; ?>

<?php if ($align == "top") : ?>
<div class="pos-media media-top"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<div class="content clearfix">

	<?php if ($align == "left" || $align == "right") : ?>
	<div class="pos-media align-<?php echo $align; ?>"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
	<?php endif; ?>

	<?php if ($this->checkPosition('content')) : ?>
	<div class="pos-content"><?php echo $this->renderPosition('content', array('style' => 'block')); ?></div>
	<?php endif; ?>

</div>

<?php if ($align == "bottom") : ?>
<div class="pos-media media-bottom"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
<p class="links"><?php echo $this->renderPosition('links'); ?></p>
<?php endif; ?>