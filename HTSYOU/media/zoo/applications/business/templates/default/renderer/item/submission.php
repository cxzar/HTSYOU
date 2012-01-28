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

<fieldset class="pos-content creation-form">
	<legend><?php echo $item->getType()->name; ?></legend>
	
	<div class="element element-name required <?php echo (null ? 'error' : ''); ?>">
		<strong><?php echo JText::_('Name'); ?></strong>
		<input type="text" name="name" size="60" value="<?php echo null; ?>" />
		<?php if (null) : ?>
			<div class="error-message"><?php echo null; ?></div>
		<?php endif; ?>
	</div>
	
	<?php if ($this->checkPosition('content')) : ?>
	<?php echo $this->renderPosition('content', array('style' => 'submission.block')); ?>
	<?php endif; ?>
	
</fieldset>

<?php if ($this->checkPosition('media')) : ?>
<fieldset class="pos-media creation-form">
	<legend><?php echo JText::_('Media'); ?></legend>
	
	<?php echo $this->renderPosition('media', array('style' => 'submission.block')); ?>
	
</fieldset>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<fieldset class="pos-meta creation-form">
	<legend><?php echo JText::_('Meta'); ?></legend>
	
	<?php echo $this->renderPosition('meta', array('style' => 'submission.block')); ?>
	
</fieldset>
<?php endif; ?>