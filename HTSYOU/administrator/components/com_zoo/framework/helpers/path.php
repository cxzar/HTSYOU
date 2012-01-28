<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: PathHelper
		Helper for managing/retrieving environment paths
*/
class PathHelper extends AppHelper {

	/* paths */
    protected $_paths = array();

    /*
		Function: register
			Register a path to a namespace.

		Parameters:
			$path - Absolute path
			$namespace - Namespace for path

		Returns:
			Void
	*/
	public function register($path, $namespace = 'default') {

	    if (!isset($this->_paths[$namespace])) {
	        $this->_paths[$namespace] = array();
	    }

	    array_unshift($this->_paths[$namespace], $path);
	}

	/*
		Function: path
			Retrieve absolute path to a file or directory

		Parameters:
			$resource - Resource with namespace, example: "assets:js/app.js"

		Returns:
			Mixed
	*/
	public function path($resource) {

		// parse resource
		extract($this->_parse($resource));

		return $this->_find($paths, $path);
	}

    /*
		Function: url
			Retrieve absolute url to a file

		Parameters:
			$resource - Resource with namespace, example: "assets:js/app.js"

		Returns:
			Mixed
	*/
	public function url($resource) {

		// init vars
	    $parts = explode('?', $resource);
	    $url   = str_replace(DIRECTORY_SEPARATOR, '/', $this->path($parts[0]));

	    if ($url) {

	        if (isset($parts[1])) {
	            $url .= '?'.$parts[1];
	        }

	        $url = JURI::root(true).'/'.$this->relative($url);
	    }

	    return $url;
	}

	/*
		Function: files
			Retrieve list of files from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function files($resource, $recursive = false, $filter = null) {
		return $this->ls($resource, 'file', $recursive, $filter);
	}

	/*
		Function: dirs
			Retrieve list of directories from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function dirs($resource, $recursive = false, $filter = null) {
		return $this->ls($resource, 'dir', $recursive, $filter);
	}

	/*
		Function: ls
			Retrieve list of files or directories from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function ls($resource, $mode = 'file', $recursive = false, $filter = null) {

		$files = array();
		$res   = $this->_parse($resource);

		foreach ($res['paths'] as $path) {
			foreach ($this->_list(realpath($path.'/'.$res['path']), '', $mode, $recursive, $filter) as $file) {
				if (!in_array($file, $files)) {
					$files[] = $file;
				}
			}
		}

		return $files;
	}

	/*
		Function: _parse
			Parse resource string.

		Parameters:
			$resource - Path to resource

		Returns:
			String
	*/
	protected function _parse($resource) {

	    // init vars
		$parts     = explode(':', $resource, 2);
		$count     = count($parts);
		$path      = '';
		$namespace = 'default';

		// parse resource path
		if ($count == 1) {
			list($path) = $parts;
		} elseif ($count == 2) {
			list($namespace, $path) = $parts;
		}

		// remove heading slash or backslash
		$path = ltrim($path, "\\/");

	    // get paths for namespace, if exists
		$paths = isset($this->_paths[$namespace]) ? $this->_paths[$namespace] : array();

		return compact('namespace', 'paths', 'path');
    }

	/*
		Function: _find
			Find file or directory in paths

		Parameters:
			$paths - Paths to search in
			$file - File or directory

		Returns:
			Mixed
	*/
	protected function _find($paths, $file) {

		$paths = (array) $paths;
		$file  = ltrim($file, "\\/");
		foreach ($paths as $path) {
			if (($fullpath = realpath("$path/$file")) && file_exists($fullpath) && stripos($fullpath, JPATH_ROOT, 0) === 0) {
				return $fullpath;
			}
		}

		return false;
	}

	/*
		Function: _list
			List files or directories in a path

		Parameters:
			$path - Paths to search in
			$mode - Mode 'file' or 'dir'
			$prefix - Prefix prepended to every file/directory
			$recursive - Recurse subdirectories

		Returns:
			Array
	*/
	protected function _list($path, $prefix = '', $mode = 'file', $recursive = false, $filter = null) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		if (is_readable($path) && is_dir($path) && ($scan = scandir($path))) {
			foreach ($scan as $file) {

				// continue if ignore match
				if (in_array($file, $ignore)) {
					continue;
				}

	            if (is_dir($path.'/'.$file)) {

					// add dir
					if ($mode == 'dir') {

						// continue if no regex filter match
						if ($filter && !preg_match($filter, $file)) {
							continue;
						}

						$files[] = $prefix.$file;
					}

					// continue if not recursive
					if (!$recursive) {
						continue;
					}

					// read subdirectory
	            	$files = array_merge($files, $this->_list($path.'/'.$file, $prefix.$file.'/', $mode, $recursive, $filter));

				} else {

					// add file
					if ($mode == 'file') {

						// continue if no regex filter match
						if ($filter && !preg_match($filter, $file)) {
							continue;
						}

						$files[] = $prefix.$file;
					}

	            }

			}
		}

		return $files;
	}

	/*
		Function: relative
			Makes a path relative to the Joomla root directory

		Parameters:
			$path - Path

		Returns:
			String - path
	*/
	public function relative($path) {
		return ltrim(preg_replace('/^'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT), '/').'/i', '', str_replace(DIRECTORY_SEPARATOR, '/', $path)), '/');
	}

}