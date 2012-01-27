<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css/js
if (strtolower(substr($GLOBALS[($this->app->joomla->isVersion('1.5') ? 'mainframe' : 'app')]->getTemplate(), 0, 3)) != 'yoo') {
	$this->app->document->addStylesheet('assets:css/reset.css');
}
$this->app->document->addStylesheet($this->template->resource.'assets/css/zoo.css');

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-alphaindex'; ?>">

	<?php if ($this->params->get('template.show_alpha_index')) : ?>
		<?php echo $this->partial('alphaindex'); ?>
	<?php endif; ?>

	<div class="details">

		<h1 class="title">
			<?php echo JText::_('Categories starting with').' '.strtoupper($this->alpha_char); ?>
		</h1>

	</div>

	<?php

		// render categories
		if (!empty($this->selected_categories)) {
			echo $this->partial('categories');
		}

	?>
	
	<?php

		// render items
		if (count($this->items)) {
			$title = JText::_('Items starting with').' '.strtoupper($this->alpha_char);
			$subtitle = '';
			echo $this->partial('items', compact('title', 'subtitle'));
		}
		
	?>

</div>