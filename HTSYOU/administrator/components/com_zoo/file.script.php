<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Com_ZOOInstallerScript {

	function install($parent) {

		// get installer
		$installer = method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;

		// try to set time limit
		@set_time_limit(0);

		// try to increase memory limit
		if((int) ini_get('memory_limit') < 32) {
			@ini_set('memory_limit', '32M');
		}

		// check requirements
		require_once($installer->getPath('source').'/admin/installation/requirements.php');

		$requirements = new AppRequirements();
		if (true !== $error = $requirements->checkRequirements()) {
			$installer->abort(JText::_('Component').' '.JText::_('Install').': '.JText::sprintf('Minimum requirements not fulfilled (%s: %s).', $error['name'], $error['info']));
			return false;
		}

		$requirements->displayResults();

		// requirements fulfilled, install the ZOO
		require_once($installer->getPath('source').'/admin/installation/zooinstall.php');
		return ZooInstall::doInstall($installer);

	}

	function uninstall($parent) {

		// get installer
		$installer = method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;

		require_once($installer->getPath('source').'/installation/zooinstall.php');
		return ZooInstall::doUninstall($installer);
	}

	function update($parent) {

		// get installer
		$installer = method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;

		if ($manifest = $parent->get('manifest')) {
			if (isset($manifest->install->sql)) {
				$utfresult = $installer->parseSQLFiles($manifest->install->sql);

				if ($utfresult === false) {
					// Install failed, rollback changes
					$installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_SQL_ERROR', JFactory::getDBO()->stderr(true)));

					return false;
				}
			}
		}

		return $this->install($parent);
	}

	function preflight($type, $parent) {

		// remove ZOO from admin menus
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__menu WHERE alias = "zoo" AND menutype = "main"');
		$db->query();
		$db->setQuery('DELETE FROM #__assets WHERE title = "com_zoo"');
		$db->query();

	}

	function postflight($type, $parent) {

		$row = JTable::getInstance('extension');
		if ($row->load($row->find(array('element' => 'com_zoo'))) && strlen($row->element)) {
			$row->client_id = 1;
			$row->store();
		}

	}
}
