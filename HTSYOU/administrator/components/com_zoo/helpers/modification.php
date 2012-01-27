<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ModificationHelper
		The general helper Class for modifications
*/
class ModificationHelper extends AppHelper {

    /*
		Function: verify
			Verifies the ZOO installation.

		Returns:
			bool - true if ZOO hasn't been modified
	*/
	public function verify() {
		$result = $this->check();
		return empty($result);
	}

    /*
		Function: check
			Checks for ZOO modifications.

		Returns:
			Array - modified files
	*/
	public function check() {

		if (!$checksum = $this->app->path->path('component.admin:checksums')) {
			throw new AppModificationException(JText::_('Unable to locate checksums file in ' . $this->app->path->path('component.admin:')));
		}

		$path = $this->app->path->path('component.admin:');
		$this->app->checksum->verify($path, $checksum, $result, array(
			create_function('$path', 'return (in_array($path, array("zoo.xml", "file.script.php")) ? "admin/" . $path : $path);'),
			create_function('$path', 'if (preg_match("#^admin#", $path)) return preg_replace("#^admin/#", "", $path);'),
		), $this->app->path->relative($path).'/');

		$path = $this->app->path->path('component.site:');
		$this->app->checksum->verify($path, $checksum, $result, array(
			create_function('$path', 'if (preg_match("#^site#", $path)) return preg_replace("#^site/#", "", $path);')
		), $this->app->path->relative($path).'/');

		return $result;
	}

    /*
		Function: clean
			Cleans any modifications from the filessystem.

		Returns:
			bool - true on success
	*/
	public function clean() {

		// check for modifications
		$results = $this->check();
		if (isset($results['unknown'])) {
			foreach ($results['unknown'] as $file) {
				if (!empty($file) && ($file = $this->app->path->path('root:'.$file))) {
					// remove unknown file
					if (!JFile::delete($file)) {
						$this->app->error->raiseWarning(0, sprintf(JText::_('Could not remove file (%s)'), $file));
					}
				}
			}
		}

		// remove empty sub folders
		$this->removeEmptySubFolders($this->app->path->path('component.admin:'));
		$this->removeEmptySubFolders($this->app->path->path('component.site:'));

		return true;

	}

    /*
		Function: removeEmptySubFolders
			Recursevly removes empty subfolders.

		Parameters:
	      $path - Path to folder.

		Returns:
			void
	*/
	public function removeEmptySubFolders($path) {
		$empty=true;
		foreach ($this->app->filesystem->readDirectory($path) as $file) {
			$empty &= is_dir($file) && $this->removeEmptySubFolders($file);
		}
		return $empty && JFolder::delete($path);
	}

}

/*
	Class: AppModificationException
*/
class AppModificationException extends AppException {}