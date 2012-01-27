<?php
/**
* @package   yoo_quantum
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die;

?>

<ul class="line bullet">
<?php foreach ($list as $item) : ?>
	<li><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></li>
<?php endforeach; ?>
</ul>