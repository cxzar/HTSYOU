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

// show description only if it has content
if (!$this->application->description) {
	$this->params->set('template.show_description', 0);
}

// show title only if it has content
if (!$this->application->getParams()->get('content.title')) {
	$this->params->set('template.show_title', 0);
}

// show image only if an image is selected
if (!($image = $this->application->getImage('content.image'))) {
	$this->params->set('template.show_image', 0);
}

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-frontpage'; ?>">
	
	<?php if ($this->params->get('template.show_title') || $this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
	<div class="details <?php echo 'alignment-'.$this->params->get('template.alignment'); ?>">
		
		<?php if ($this->params->get('template.show_title')) : ?>
			<h1 class="title"><?php echo $this->application->getParams()->get('content.title'); ?></h1>
			
			<?php if ($this->application->getParams()->get('content.subtitle')) : ?>
			<h2 class="subtitle"><?php echo $this->application->getParams()->get('content.subtitle'); ?></h2>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
		<div class="description">
			<?php if ($this->params->get('template.show_image')) : ?>
				<img class="image" src="<?php echo $image['src']; ?>" title="<?php echo $this->application->getParams()->get('content.title'); ?>" alt="<?php echo $this->application->getParams()->get('content.title'); ?>" <?php echo $image['width_height']; ?>/>
			<?php endif; ?>
			<?php if ($this->params->get('template.show_description')) echo $this->application->getText($this->application->description); ?>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>	

	<?php

		// render categories
		$has_categories = false;
		if ($this->category->childrenHaveItems()) {
			
			// init vars
			$has_categories = true;
			$i = 0;
			
			echo '<div class="frontpage-categories">';
			
			// render rows
			foreach($this->selected_categories as $category) {
				if ($category && !$category->totalItemCount()) continue;
				if ($i % $this->params->get('template.categories_cols') == 0) echo ($i > 0 ? '</div><div class="row">' : '<div class="row first-row">');
				$firstcell = ($i % $this->params->get('template.categories_cols') == 0) ? 'first-cell' : null;
				echo '<div class="width'.intval(100 / $this->params->get('template.categories_cols')).' '.$firstcell.'">'.$this->partial('frontpage_category', compact('category')).'</div>';
				$i++;
			}
			echo '</div>';
			
			echo '</div>';
			
		}
		
	?>
	
	<?php

		// render items
		if (count($this->items)) {
			echo $this->partial('items', compact('has_categories'));
		}
		
	?>

</div>