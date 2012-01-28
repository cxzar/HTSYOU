<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div>

    <?php echo $this->app->html->_('control.text', $this->getControlName('value'), $this->get('value'), 'size="60" title="'.JText::_('Link').'"'); ?>

    <?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">

			<div class="row">
				<?php echo $this->app->html->_('control.text', $this->getControlName('text'), $this->get('text'), 'size="60" title="'.JText::_('Text').'" placeholder="'.JText::_('Text').'"'); ?>
			</div>

			<div class="row">
				<strong><?php echo JText::_('New window'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', $this->getControlName('target'), '', $this->get('target')) ?>
			</div>

			<div class="row short">
				<?php echo $this->app->html->_('control.text', $this->getControlName('custom_title'), $this->get('custom_title'), 'size="60" title="'.JText::_('Title').'" placeholder="'.JText::_('Title').'"'); ?>
			</div>

			<div class="row short">
				<?php echo $this->app->html->_('control.text', $this->getControlName('rel'), $this->get('rel'), 'size="60" title="'.JText::_('Lightbox').'" placeholder="'.JText::_('Lightbox').'"'); ?>
			</div>

		</div>
	</div>

    <?php endif; ?>

</div>