<?php
/**
* @package   ZOO Category
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
$zoo->document->addStylesheet('mod_zoocategory:tmpl/list/style.css');

$count = count($categories);

?>

<?php if ($count) : ?>

<ul class="zoo-category-list">
	<?php foreach ($categories as $category) : ?>
		<?php echo $zoo->categorymodule->render($category, $params, 2); ?>
	<?php endforeach; ?>
</ul>

<?php else : ?>
<?php echo JText::_('COM_ZOO_NO_CATEGORIES_FOUND'); ?>
<?php endif; ?>