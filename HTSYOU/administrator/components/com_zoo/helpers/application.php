<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
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

}
