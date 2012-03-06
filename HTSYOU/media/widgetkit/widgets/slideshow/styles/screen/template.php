<?php 
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

	$widget_id  = $widget->id.'-'.uniqid();
	$settings   = $widget->settings;
	$navigation = array();
	$captions   = array();

	$i = 0;
?>

<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow wk-slideshow-screen" data-widgetkit="slideshow" data-options='<?php echo json_encode($settings); ?>'>
	<div>
		<ul class="slides">

			<?php foreach ($widget->items as $key => $item) : ?>
			<?php
				$navigation[] = '<li><span></span></li>';
				$captions[]   = '<li>'.(isset($item['caption']) ? $item['caption']:"").'</li>';
			
				/* Lazy Loading */
				$item["content"] = ($i==$settings['index']) ? $item["content"] : $this['image']->prepareLazyload($item["content"]);
			?>
			<li>
				<article class="wk-content clearfix"><?php echo $item['content']; ?></article>
			</li>
			<?php $i=$i+1;?>
			<?php endforeach; ?>
		</ul>
		<?php if ($settings['buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		<div class="caption"></div><ul class="captions"><?php echo implode('', $captions);?></ul>
	</div>
	<?php echo ($settings['navigation'] && count($navigation)) ? '<ul class="nav">'.implode('', $navigation).'</ul>' : '';?>
</div>