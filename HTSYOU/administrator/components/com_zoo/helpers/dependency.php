<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DependencyHelper
		The general helper Class for dependencies
*/
class DependencyHelper extends AppHelper {

    /*
		Function: check
			Checks if ZOO extensions meet the required version

		Returns:
			bool - true if all requirements are met
	*/
	public function check() {
		if ($dependencies = $this->app->path->path("component.admin:installation/dependencies.config")) {
			if ($dependencies = json_decode(JFile::read($dependencies))) {
				foreach ($dependencies as $dependency) {
					$required  = $dependency->version;
					$manifest = $this->app->path->path('root:'.$dependency->manifest);
					if ($required && is_file($manifest) && is_readable($manifest)) {
						if ($xml = simplexml_load_file($manifest)) {
							if (version_compare($required, (string) $xml->version) > 0) {
								$name = isset($dependency->url) ? "<a href=\"{$dependency->url}\">{$xml->name}</a>" : (string) $xml->name;
								$this->app->error->raiseNotice(0, sprintf("The %s extension requires an update for the Zoo to run correctly.", $name));
							}
						}
					}
				}
			}
		}
	}

}