<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ApplicationHelper
   The Helper Class for application
*/
class ApplicationHelper extends AppHelper {

	/*
		Function: getApplications
			Get all applications for an application group.

		Parameters:
			$group - Application group

		Returns:
			Array - The applications of the application group
	*/
    public function getApplications($group = false) {
        // get application instances for selected group
        $applications = array();
        foreach ($this->app->table->application->all(array('order' => 'name')) as $application) {
            if (!$group || $application->getGroup() == $group) {
                $applications[$application->id] = $application;
            }
        }
        return $applications;
    }

	/*
		Function: groups
			Get all application groups.

		Returns:
			Array - The application groups
	*/
	public function groups() {

		// get applications
		$apps = array();

		if ($folders = $this->app->path->dirs('applications:')) {
			foreach ($folders as $folder) {
				if ($this->app->path->path("applications:$folder/application.xml")) {
					$apps[$folder] = $this->app->object->create('Application');
					$apps[$folder]->setGroup($folder);
				}
			}
		}

		return $apps;
	}

}
