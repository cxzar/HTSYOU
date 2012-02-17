<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class BlankComponentViewDefault extends JView
{
	function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		$params = $app->getParams();

		$menus	= $app->getMenu();
		$menu	= $menus->getActive();

		if (is_object($menu)) {
			$menu_params = new JRegistry;
			$menu_params->loadJSON($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	JText::_('Blank Component'));
			}
		}
		else {
			$params->set('page_title',	JText::_('Blank Component'));
		}

		$title = $params->get('page_title');
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords')) 
		{
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots')) 
		{
			$this->document->setMetadata('robots', $params->get('robots'));
		}

		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}
