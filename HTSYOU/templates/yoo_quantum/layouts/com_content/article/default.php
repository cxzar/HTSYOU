<?php
/**
* @package   yoo_quantum
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();

?>

<div id="system">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<article class="item">

		<?php if ($params->get('show_title')) : ?>
		<header>
		
			<?php if (!$this->print) : ?>
				<?php if ($params->get('show_email_icon')) : ?>
				<div class="icon email"><?php echo JHtml::_('icon.email',  $this->item, $params); ?></div>
				<?php endif; ?>
			
				<?php if ($params->get('show_print_icon')) : ?>
				<div class="icon print"><?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?></div>
				<?php endif; ?>
			<?php else : ?>
				<div class="icon printscreen"><?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?></div>
			<?php endif; ?>
		
			<?php if ($params->get('show_create_date')) : ?>
			<time datetime="<?php echo substr($this->item->created, 0,10); ?>" pubdate>
				<span class="day"><?php echo JHTML::_('date',$this->item->created, JText::_('d')); ?></span>
				<span class="month"><?php echo JHTML::_('date',$this->item->created, JText::_('M')); ?></span>
			</time>
			<?php endif; ?>
	
			<h1 class="title">
				<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
					<a href="<?php echo $this->item->readmore_link; ?>"><?php echo $this->escape($this->item->title); ?></a>
				<?php else : ?>
						<?php echo $this->escape($this->item->title); ?>
				<?php endif; ?>
			</h1>

			<?php if (($params->get('show_author') && !empty($this->item->author)) || $params->get('show_category')) : ?>
			<p class="meta">
		
				<?php
					
					if ($params->get('show_author') && !empty($this->item->author )) {
						
						$author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;
						
						if (!empty($this->item->contactid) && $params->get('link_author') == true) {
						
							$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
							$item = JSite::getMenu()->getItems('link', $needle, true);
							$cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
						
							echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author));
						} else {
							echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author);
						}
						echo '. ';
	
					}
				
					if ($params->get('show_category')) {
						echo JText::_('TPL_WARP_POSTED_IN').' ';
						$title = $this->escape($this->item->category_title);
						$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
						if ($params->get('link_category') AND $this->item->catslug) {
							echo $url;
						} else {
							echo $title;
						}
					}
				
				?>	
			
			</p>
			<?php endif; ?>

		</header>
		<?php endif; ?>
	
		<?php
		
			if (!$params->get('show_intro')) {
				echo $this->item->event->afterDisplayTitle;
			}
		
			echo $this->item->event->beforeDisplayContent;

			if (isset ($this->item->toc)) {
				echo $this->item->toc;
			}
			
		?>

		<div class="content clearfix">
		<?php
		
			if ($params->get('access-view')) {
				echo $this->item->text;
			
			// optional teaser intro text for guests
			} elseif ($params->get('show_noauth') == true AND $user->get('guest')) {
				
				echo $this->item->introtext;
				
				// optional link to let them register to see the whole article.
				if ($params->get('show_readmore') && $this->item->fulltext != null) {
					$link1 = JRoute::_('index.php?option=com_users&view=login');
					$link = new JURI($link1);
					echo '<p class="links">';
					echo '<a href="'.$link.'">';
					$attribs = json_decode($this->item->attribs);
		
					if ($attribs->alternative_readmore == null) {
						echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
					} elseif ($readmore = $this->item->alternative_readmore) {
						echo $readmore;
						if ($params->get('show_readmore_title', 0) != 0) {
							echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						}
					} elseif ($params->get('show_readmore_title', 0) == 0) {
						echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
					} else {
						echo JText::_('COM_CONTENT_READ_MORE');
						echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					}
					
					echo '</a></p>';
				}
			}
			
		?>
		</div>

		<?php if ($canEdit) : ?>
		<p class="edit"><?php echo JHtml::_('icon.edit', $this->item, $params); ?> <?php echo JText::_('TPL_WARP_EDIT_ARTICLE'); ?></p>
		<?php endif; ?>

		<?php echo $this->item->event->afterDisplayContent; ?>
	
	</article>

</div>