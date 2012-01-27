<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('users.spacer', array('JHtmlUsers','spacer'));

?>

<?php foreach ($fieldsets as $group => $fieldset): ?>
	<?php $fields = $this->form->getFieldset($fieldset->name); ?>
	<?php if (count($fields)): ?>
		<fieldset>
			<?php if (isset($fieldset->label)): ?>
			<legend><?php echo JText::_($fieldset->label); ?></legend>
			<?php endif;?>
			<?php foreach ($fields as $field): ?>
				<?php if (!$field->hidden): ?>
				<div>
					<?php echo $field->title; ?>
					<?php if (JHtml::isRegistered('users.'.$field->id)):?>
						<?php echo JHtml::_('users.'.$field->id, $field->value);?>
					<?php elseif (JHtml::isRegistered('users.'.$field->fieldname)):?>
						<?php echo JHtml::_('users.'.$field->fieldname, $field->value);?>
					<?php elseif (JHtml::isRegistered('users.'.$field->type)):?>
						<?php echo JHtml::_('users.'.$field->type, $field->value);?>
					<?php else:?>
						<?php echo JHtml::_('users.value', $field->value);?>
					<?php endif;?>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>
<?php endforeach; ?>