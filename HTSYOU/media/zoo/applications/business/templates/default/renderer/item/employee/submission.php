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
	<legend><?php echo $form->getItem()->getType()->name; ?></legend>
	
	<div class="element element-name required <?php echo ($form->hasError('name') ? 'error' : ''); ?>">
		<strong><?php echo JText::_('Name'); ?></strong>
		<input type="text" name="name" size="60" value="<?php echo $form->getTaintedValue('name'); ?>" />
		<?php if ($form->hasError('name')) : ?>
			<div class="error-message"><?php echo $form->getError('name'); ?></div>
		<?php endif; ?>
	</div>
	
	<?php if ($this->checkPosition('content')) : ?>
	<?php echo $this->renderPosition('content', array('style' => 'submission.block')); ?>
	<?php endif; ?>
	
</fieldset>