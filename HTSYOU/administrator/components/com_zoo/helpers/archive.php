<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ArchiveHelper
		Archive helper class
*/
class ArchiveHelper extends AppHelper {

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// increase memory limit
		@ini_set('memory_limit', '256M');

		$app->loader->register('PclZip', 'libraries:pcl/pclzip.lib.php');

	}

	/*
		Function: open
			Open archive file

		Returns:
			Mixed
	*/
	public function open($file, $format = 'zip') {

		// auto-detect format
		if (!$format) {
			$format = $this->format($file);
		}

		// create archive object
		if ($format == 'zip') {
			return new PclZip($file);
		}

		return null;
	}

	/*
		Function: format
			Get archive format based on filename

		Returns:
			Mixed
	*/
	public function format($file) {

		// detect .zip format
		if (preg_match('/\.zip$/i', $file)) {
			return 'zip';
		}

		// detect .tar format
		if (preg_match('/\.tar$|\.tar\.gz$|\.tgz$|\.tar\.bz2$|\.tbz2$/i', $file)) {
			return 'tar';
		}

		return null;
	}

}