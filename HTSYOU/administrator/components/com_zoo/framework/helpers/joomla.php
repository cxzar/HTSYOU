<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: JoomlaHelper
		The general helper Class for parameter
*/
class JoomlaHelper extends AppHelper {

	protected $_version;

	public function __construct($app) {
		parent::__construct($app);

		JLoader::import('joomla.version');

		$this->_version = new JVersion();
	}

	/*
		Function: getVersion
			Get Joomla Version Number.

		Returns:
			String - The Joomla short version
	*/
	public function getVersion() {
		return $this->_version->getShortVersion();
	}

	/*
		Function: isVersion
			Checks current Joomla version.

		Parameters:
			$version - version string to check against
			$release - only compare release version

		Returns:
			Boolean
	*/
	public function isVersion($version, $release = true) {
		return $release ? $this->_version->RELEASE == $version : $this->getVersion() == $version;
	}

	/*
		Function: getDefaultAccess
			Get the default access group.

		Returns:
			String - the default access group id
	*/
	public function getDefaultAccess() {
		return $this->isVersion('1.5') ? 0 : $this->app->system->application->getCfg('access');
	}

	/* @deprecated */
	public function isJoomla15() {
		return $this->isVersion('1.5');
	}

}
