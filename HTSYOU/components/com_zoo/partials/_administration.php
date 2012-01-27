<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addScript('assets:js/autosuggest.js');
$this->app->document->addScript('assets:js/tag.js');

$tags = $this->form->hasError('tags') ? $this->form->getTaintedValue('tags') : $this->form->getValue('tags');
$tags = $tags ? $tags : array();

?>

<fieldset class="administration creation-form">
	<legend>Administration</legend>

	<div class="floatbox">

		<div class="width50">

			<div class="element">
				<strong><?php echo JText::_('Published'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', 'state', '', $this->form->getTaintedValue('state')); ?>
				<?php if ($this->form->hasError('state')) : ?><div class="error-message"><?php echo $this->form->getError('state'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Searchable'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', 'searchable', '', $this->form->getTaintedValue('searchable')); ?>
				<?php if ($this->form->hasError('searchable')) : ?><div class="error-message"><?php echo $this->form->getError('searchable'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Comments'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', 'enable_comments', '', $this->form->getTaintedValue('enable_comments')); ?>
				<?php if ($this->form->hasError('enable_comments')) : ?><div class="error-message"><?php echo $this->form->getError('enable_comments'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Frontpage'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', 'frontpage', '', $this->form->getTaintedValue('frontpage')); ?>
				<?php if ($this->form->hasError('frontpage')) : ?><div class="error-message"><?php echo $this->form->getError('frontpage'); ?></div><?php endif; ?>
			</div>

			<div class="element element-publish_up<?php echo ($this->form->hasError('publish_up') ? ' error' : ''); ?>">
				<strong><?php echo JText::_('Start Publishing'); ?></strong>
				<?php echo $this->app->html->_('zoo.calendar', $this->form->getTaintedValue('publish_up'), 'publish_up', 'publish_up', array('class' => 'calendar-element'), true); ?>
				<?php if ($this->form->hasError('publish_up')) : ?><div class="error-message"><?php echo $this->form->getError('publish_up'); ?></div><?php endif; ?>
			</div>

			<div class="element element-publish_down<?php echo ($this->form->hasError('publish_down') ? ' error' : ''); ?>">
				<?php
					if (!($publish_down = $this->form->getTaintedValue('publish_down')) || $publish_down == $this->app->database->getNullDate()) {
						$publish_down  = JText::_('Never');
					}
				?>
				<strong><?php echo JText::_('Finish Publishing'); ?></strong>
				<?php echo $this->app->html->_('zoo.calendar', $publish_down, 'publish_down', 'publish_down', array('class' => 'calendar-element'), true); ?>
				<?php if ($this->form->hasError('publish_down')) : ?><div class="error-message"><?php echo $this->form->getError('publish_down'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Access Level'); ?></strong>
				<?php echo $this->app->html->_('control.accesslevel', array(), 'access', 'class="inputbox"', 'value', 'text', $this->form->getTaintedValue('access')); ?>
				<?php if ($this->form->hasError('access')) : ?><div class="error-message"><?php echo $this->form->getError('access'); ?></div><?php endif; ?>
			</div>

		</div>

		<div class="width50">

			<div class="element">
				<strong><?php echo JText::_('Categories'); ?></strong>
				<div><?php echo $this->app->html->_('zoo.categorylist', $this->application, array(), 'categories[]', 'size="15" multiple="multiple"', 'value', 'text', $this->form->getTaintedValue('categories')); ?></div>
				<?php if ($this->form->hasError('categories')) : ?><div class="error-message"><?php echo $this->form->getError('categories'); ?></div><?php endif; ?>
			</div>

		</div>

	</div>

	<div id="tag-area">
		<input type="text" value="<?php echo implode(', ', $tags); ?>" placeholder="<?php echo JText::_('Add tag'); ?>" />
		<p><?php echo JText::_('Choose from the most used tags');?>:</p>
		<?php if (count($this->lists['most_used_tags'])) : ?>
		<div class="tag-cloud">
			<?php foreach ($this->lists['most_used_tags'] as $tag) :?>
				<a title="<?php echo $tag->items . ' ' . ($tag->items == 1 ? JText::_('item') : JText::_('items')); ?>"><?php echo $tag->name; ?></a>
			<?php endforeach;?>
		</div>
		<?php endif; ?>
	</div>

</fieldset>

<script type="text/javascript">
	jQuery(function($) {
		$('#item-submission #tag-area').Tag({ url: '<?php echo $this->app->link(array('controller' => 'submission'), false); ?>', addButtonText: '<?php echo JText::_('Add Tag'); ?>' });
	});
</script>