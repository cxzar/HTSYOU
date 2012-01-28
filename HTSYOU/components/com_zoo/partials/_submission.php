<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
$this->app->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
$this->app->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
$this->app->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
$this->app->document->addStylesheet('assets:css/submission.css');
$this->app->document->addScript('assets:js/submission.js');
$this->app->document->addScript('assets:js/placeholder.js');
$this->app->document->addScript('assets:js/item.js');

if ($this->submission->showTooltip()) {
	$this->app->html->_('behavior.tooltip');
}

?>

<?php if ($this->errors): ?>
	<?php $msg = count($this->errors) > 1 ? JText::_('Oops. There were errors in your submission.') : JText::_('Oops. There was an error in your submission.'); ?>
	<?php $msg .= ' '.JText::_('Please take a look at all highlighted fields, correct your data and try again.'); ?>
	<p class="message"><?php echo $msg; ?></p>
<?php endif; ?>

<form id="item-submission" action="<?php echo JRoute::_($this->app->route->submission($this->submission, $this->type->id, $this->hash, $this->item->id, $this->redirectTo)); ?>" method="post" name="submissionForm" accept-charset="utf-8" enctype="multipart/form-data">

	<?php

		echo $this->renderer->render($this->layout_path, array('item' => $this->item, 'submission' => $this->submission));

	?>

	<p class="info"><?php echo JText::_('REQUIRED_INFO'); ?></p>

	<div class="submit">
		<button type="submit" id="submit-button" class="button-green"><?php echo JText::_('Submit Item'); ?></button>
		<?php if (!empty($this->redirectTo)) : ?>
			<a href="<?php echo JRoute::_($this->app->route->mysubmissions($this->submission)); ?>" id="cancel-button"><?php echo JText::_('Cancel'); ?></a>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->app->component->self->name; ?>" />
	<input type="hidden" name="controller" value="submission" />
	<input type="hidden" name="task" value="save" />

	<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#item-submission').EditItem();
		$('#item-submission').Submission({ uri: '<?php echo JURI::root(); ?>' });
	});
</script>