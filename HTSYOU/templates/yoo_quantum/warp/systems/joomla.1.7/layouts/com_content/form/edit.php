<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div id="system">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<form class="submission box" action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
		<fieldset>
			<legend><?php echo JText::_('JEDITOR'); ?></legend>
	
				<div class="formelm">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>
		
				<?php if (is_null($this->item->id)):?>
				<div class="formelm">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>
				<?php endif; ?>

				<?php echo $this->form->getInput('articletext'); ?>
	
		</fieldset>
	
		<fieldset>
			<legend><?php echo JText::_('COM_CONTENT_PUBLISHING'); ?></legend>
			
			<div class="formelm">
				<?php echo $this->form->getLabel('catid'); ?>
				<?php
					if($this->params->get('enable_category', 0) == 1) {
						echo '<span>'.$this->category_title.'</span>';
					} else {
						echo $this->form->getInput('catid');
					}
				?>
			</div>
			
			<div class="formelm">
				<?php echo $this->form->getLabel('created_by_alias'); ?>
				<?php echo $this->form->getInput('created_by_alias'); ?>
			</div>
	
			<?php if ($this->item->params->get('access-change')): ?>
			
				<div class="formelm">
					<?php echo $this->form->getLabel('state'); ?>
					<?php echo $this->form->getInput('state'); ?>
				</div>
		
				<div class="formelm">
					<?php echo $this->form->getLabel('featured'); ?>
					<?php echo $this->form->getInput('featured'); ?>
				</div>
		
				<div class="formelm">
					<?php echo $this->form->getLabel('publish_up'); ?>
					<?php echo $this->form->getInput('publish_up'); ?>
				</div>
				<div class="formelm">
					<?php echo $this->form->getLabel('publish_down'); ?>
					<?php echo $this->form->getInput('publish_down'); ?>
				</div>
		
			<?php endif; ?>
		
			<div class="formelm">
				<?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?>
			</div>
			
			<?php if (is_null($this->item->id)):?>
			<div class="form-note">
				<p><?php echo JText::_('COM_CONTENT_ORDERING'); ?></p>
			</div>
			<?php endif; ?>
			
		</fieldset>
	
		<fieldset>
			<legend><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></legend>
			<div class="formelm-area">
			<?php echo $this->form->getLabel('language'); ?>
			<?php echo $this->form->getInput('language'); ?>
			</div>
		</fieldset>
	
		<fieldset>
			<legend><?php echo JText::_('COM_CONTENT_METADATA'); ?></legend>
			
			<div class="formelm-area">
				<?php echo $this->form->getLabel('metadesc'); ?>
				<?php echo $this->form->getInput('metadesc'); ?>
			</div>
			
			<div class="formelm-area">
				<?php echo $this->form->getLabel('metakey'); ?>
				<?php echo $this->form->getInput('metakey'); ?>
			</div>

		</fieldset>
		
		<div class="submit">
			<button type="button" onclick="Joomla.submitbutton('article.save')"><?php echo JText::_('JSAVE') ?></button>
			<button type="button" onclick="Joomla.submitbutton('article.cancel')"><?php echo JText::_('JCANCEL') ?></button>
		</div>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
		<?php if($this->params->get('enable_category', 0) == 1) : ?>
		<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>" />
		<?php endif; ?>
		<?php echo JHtml::_( 'form.token' ); ?>
		
	</form>
</div>