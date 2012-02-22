<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ChecksumHelper
		Checksum helper class
*/
class ChecksumHelper extends AppHelper {

	/*
		Function: create
			Create file checksums

		Parameters:
			$path - Path to files
			$checksums - Checksum filename

		Returns:
			Boolean
	*/
	public function create($path, $filename = 'checksums') {

		$path  = rtrim(str_replace(DIRECTORY_SEPERATOR, '/', $path), '/').'/';
		$files = $this->_readDirectory($path);

		if (is_array($files)) {
			$checksums = '';

			foreach ($files as $file) {

				// dont include the checksum file itself
				if ($file == $filename) {
					continue;
				}

				$checksums .= md5_file($path.$file)." {$file}\n";
			}

			return file_put_contents($path.$filename, $checksums);
		}

		return false;
	}

	/*
		Function: verify
			Verify file checksums

		Parameters:
			$path - Path to files
			$checksum - Checksum file
			$log - Log array
			$filter - Array of filter callback functions
			$prefix - Prefix

		Returns:
			Boolean
	*/
	public function verify($path, $checksum, &$log = null, array $filter = array(), $prefix = '') {
		$path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/').'/';

		if ($rows = file($checksum)) {

			$checksum_files = array();
			foreach ($rows as $row) {
				list($md5, $file) = explode(' ', trim($row), 2);

				foreach ($filter as $callback) {
					if ($callback && !($file = call_user_func($callback, $file))) {
						continue 2;
					}
				}

				$checksum_files[] = $file;

				if (!file_exists($path.$file)) {
					$log['missing'][] = $prefix.$file;
				} elseif (md5_file($path.$file) != $md5) {
					$log['modified'][] = $prefix.$file;
				}
			}

			foreach ($this->_readDirectory($path) as $file) {
				if (!in_array($file, $checksum_files) && !preg_match('/'.preg_quote($file, '/').'$/i', $checksum)) {
					$log['unknown'][] = $prefix.$file;
				}
			}

		}

		return empty($log);
	}

	/*
		Function: _readDirectory
			Read files form a directory

		Parameters:
			$path - Path to files
			$prefix - Prefix
			$recursive - Recursive

		Returns:
			Array
	*/
	protected function _readDirectory($path, $prefix = '', $recursive = true) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		foreach (scandir($path) as $file) {

			// ignore file ?
	        if (in_array($file, $ignore)) {
				continue;
			}

			// get files
            if (is_dir($path.'/'.$file) && $recursive) {
            	$files = array_merge($files, $this->_readDirectory($path.'/'.$file, $prefix.$file.'/', $recursive));
			} else {
				$files[] = $prefix.$file;
            }
		}

		return $files;
	}

}