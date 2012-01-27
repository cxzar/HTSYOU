<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class JElementZooSubmission extends JElement {

	var	$_name = 'ZooSubmission';

	function fetchElement($name, $value, &$node, $control_name) {

		// load config
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// get app
		$app = App::getInstance('zoo');

		$app->html->_('behavior.modal', 'a.modal');
		$app->document->addStylesheet('joomla:elements/zoosubmission.css');
		$app->document->addScript('joomla:elements/zoosubmission.js');

		// init vars
		$params	= $app->parameterform->convertParams($this->_parent);
		$table  = $app->table->application;

        $show_types = $node->attributes('types') || @$node->attributes()->types;

		// create application/category select
        $submissions = array();
		$types       = array();
		$app_options = array($app->html->_('select.option', '', '- '.JText::_('Select Application').' -'));

		foreach ($table->all(array('order' => 'name')) as $application) {
			// application option
			$app_options[$application->id] = $app->html->_('select.option', $application->id, $application->name);

            // create submission select
            $submission_options = array();
            foreach($application->getSubmissions() as $submission) {
                $submission_options[$submission->id] = $app->html->_('select.option', $submission->id, $submission->name);

                if ($show_types) {
                    $type_options = array();
                    $type_objects = $submission->getSubmittableTypes();
                    if (!count($type_objects)) {
                        unset($submission_options[$submission->id]);
                        continue;
                    }

                    foreach ($submission->getTypes() as $type) {
                        $type_options[] = $app->html->_('select.option', $type->id, $type->name);
                    }

                    $attribs = 'class="type submission-'.$submission->id.' app-'.$application->id.'" role="'.$control_name.'[type]"';
                    $types[] = $app->html->_('select.genericlist', $type_options, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'));
                }
            }

            if (!count($submission_options)) {
                unset($app_options[$application->id]);
                continue;
            }

			$attribs = 'class="submission app-'.$application->id.'" role="'.$control_name.'[submission]"';
			$submissions[] = $app->html->_('select.genericlist', $submission_options, $control_name.'[submission]', $attribs, 'value', 'text', $params->get('submission'));
		}

		// create html
		$html[] = '<div id="'.$name.'" class="zoo-submission">';

		// create application html
		$html[] = $app->html->_('select.genericlist', $app_options, $control_name.'['.$name.']', 'class="application"', 'value', 'text', $value);

		// create submission html
		$html[] = '<div class="submissions">'.implode("\n", $submissions).'</div>';

		// create types html
        if ($show_types) {
            $html[] = '<div class="types">'.implode("\n", $types).'</div>';
        }

		$html[] = '</div>';

		$javascript  = 'jQuery(function($) { jQuery("#'.$name.'").ZooSubmission(); });';
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return implode("\n", $html).$javascript;
	}

}