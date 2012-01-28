<?php 
/**
* @package   Widgetkit Bonus Styles
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$sets      = array_chunk($widget->items, $settings['items_per_set']);

	foreach (array_keys($sets) as $s) {
		$nav[] = '<li><span></span></li>';
	}

?>

<div id="showcase-<?php echo $widget_id; ?>" class="wk-slideshow-showcaseboxcloud" data-widgetkit="showcase" data-options='<?php echo json_encode($settings); ?>'>

	<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow">
		<div class="slides-container">
			<ul class="slides">
				<?php foreach ($widget->items as $key => $item) : ?>
				<li>
					<article class="wk-content clearfix"><?php echo $item['content']; ?></article>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php if ($settings['buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		</div>
	</div>

	<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset <?php if (!$settings['slideset_buttons']) echo 'no-buttons'; ?>">
		<div>
			<div class="sets">
				<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $item) : ?>
					<li>
						<div><?php echo $item['navigation']; ?></div>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endforeach; ?>
			</div>
			<?php if ($settings['slideset_buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		</div>
	</div>
	
</div>