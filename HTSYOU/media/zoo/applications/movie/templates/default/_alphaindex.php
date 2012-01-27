<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="alpha-index <?php if ($this->params->get('template.alignment') == 'center') echo 'alpha-index-center'; ?>">			
	<?php echo $this->alpha_index->render(); ?>
</div>