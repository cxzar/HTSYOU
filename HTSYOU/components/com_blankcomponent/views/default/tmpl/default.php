<?php

// no direct access
defined('_JEXEC') or die;
?>
<!-- Blank Component 1.7.0 starts here -->
<div class="blank<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) :?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>
</div>
<!-- Blank Component 1.7.0 ends here -->
