<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ConfigurationController
		The controller class for application configuration
*/
class ConfigurationController extends AppController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->application;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// register tasks
		$this->registerTask('applyassignelements', 'saveassignelements');
		$this->registerTask('apply', 'save');
	}

	public function display() {

		// set toolbar items
		$this->app->system->application->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Config')));
		$this->app->toolbar->apply();
		$this->app->zoo->toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// template select
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Template').' -'));
		foreach ($this->application->getTemplates() as $template) {
			$options[] = $this->app->html->_('select.option', $template->name, $template->getMetaData('name'));
		}

		$this->lists['select_template'] = $this->app->html->_('select.genericlist',  $options, 'template', '', 'value', 'text', $this->params->get('template'));

		// display view
		$this->getView()->setLayout('application')->display();
	}

	public function save() {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array');

		try {

			// bind post
			self::bind($this->application, $post, array('params'));

			// set params
			$params = $this->application
				->getParams()
				->remove('global.')
				->set('template', @$post['template'])
				->set('global.config.', @$post['params']['config'])
				->set('global.template.', @$post['params']['template']);

			if (isset($post['addons']) && is_array($post['addons'])) {
				foreach ($post['addons'] as $addon => $value) {
					$params->set("global.$addon.", $value);
				}
			}

			// save application
			$this->table->save($this->application);

			// set redirect message
			$msg = JText::_('Application Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Application').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function getApplicationParams() {

		// init vars
		$template     = $this->app->request->getCmd('template');

		// get params
		$this->params = $this->application->getParams();

		// set template
		$this->params->set('template', $template);

		// display view
		$this->getView()->setLayout('_applicationparams')->display();
	}

	public function importExport() {

		// set toolbar items
		$this->app->system->application->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Import / Export')));
		$this->app->zoo->toolbarHelp();

		$this->exporter = $this->app->export->getExporters('Zoo v2');

		// display view
		$this->getView()->setLayout('importexport')->display();
	}

	public function importFrom() {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		$exporter = $this->app->request->getString('exporter');

		try {

			$xml = $this->app->export->create($exporter)->export();

			$file = rtrim($this->app->system->application->getCfg('tmp_path'), '\/') . '/' . $this->app->utility->generateUUID() . '.tmp';
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
			JFile::write($file, $xml);

		} catch (Exception $e) {

			// raise error on exception
			$this->app->error->raiseNotice(0, JText::_('Error During Export').' ('.$e.')');
			$this->setRedirect($this->baseurl.'&task=importexport', $msg);

		}

		$this->_import($file);

	}

	public function import() {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		$userfile = null;

		$jsonfile = $this->app->request->getVar('import-json', array(), 'files', 'array');

		try {

			// validate
			$validator = $this->app->validator->create('file', array('extensions' => array('json')));
			$userfile = $validator->clean($jsonfile);
			$type = 'json';

		} catch (AppValidatorException $e) {}

		$csvfile = $this->app->request->getVar('import-csv', array(), 'files', 'array');

		try {

			// validate
			$validator = $this->app->validator->create('file', array('extensions' => array('csv')));
			$userfile = $validator->clean($csvfile);
			$type = 'csv';

		} catch (AppValidatorException $e) {}

		if (!empty($userfile)) {
			$file = rtrim($this->app->system->application->getCfg('tmp_path'), '\/') . '/' . basename($userfile['tmp_name']);
			if (JFile::upload($userfile['tmp_name'], $file)) {

				$this->_import($file, $type);

			} else {
				// raise error on exception
				$this->app->error->raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
				$this->setRedirect($this->baseurl.'&task=importexport', $msg);
			}
		} else {
			// raise error on exception
			$this->app->error->raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
			$this->setRedirect($this->baseurl.'&task=importexport', $msg);
		}


	}

	public function importCSV() {

		$file = $this->app->request->getCmd('file', '');
		$file = rtrim($this->app->system->application->getCfg('tmp_path'), '\/') . '/' . $file;

		$this->_import($file, 'importcsv');
	}

	protected function _import($file, $type = 'json') {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// set toolbar items
		$this->app->system->application->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Import').': '.$this->application->name));
		$this->app->toolbar->cancel('importexport', 'Cancel');
		$this->app->zoo->toolbarHelp();

		// set_time_limit doesn't work in safe mode
        if (!ini_get('safe_mode')) {
		    @set_time_limit(0);
        }

		$layout = '';
		switch ($type) {
			case 'xml':
				$this->app->error->raiseWarning(0, 'XML import is not supported since ZOO 2.5!');
				$this->importExport();
				break;
			case 'json':
				if (JFile::exists($file) && $data = $this->app->data->create(JFile::read($file))) {

					$this->info = $this->app->import->getImportInfo($data);
					$this->file = basename($file);

				} else {

					// raise error on exception
					$this->app->error->raiseNotice(0, JText::_('Error Importing (Not a valid JSON file)'));
					$this->setRedirect($this->baseurl.'&task=importexport', $msg);

				}
				$layout = 'importjson';
				break;
			case 'csv':

				$this->file = basename($file);

				$layout = 'configcsv';
				break;
			case 'importcsv':
				$this->contains_headers = $this->app->request->getBool('contains-headers', false);
				$this->field_separator	= $this->app->request->getString('field-separator', ',');
				$this->field_separator	= empty($this->field_separator) ? ',' : substr($this->field_separator, 0, 1);
				$this->field_enclosure	= $this->app->request->getString('field-enclosure', '"');
				$this->field_enclosure	= empty($this->field_enclosure) ? '"' : substr($this->field_enclosure, 0, 1);

				$this->info = $this->app->import->getImportInfoCSV($file, $this->contains_headers, $this->field_separator, $this->field_enclosure);
				$this->file = basename($file);

				$layout = 'importcsv';
				break;
		}

		// display view
		$this->getView()->setLayout($layout)->display();

	}

	public function doImport() {

		// init vars
		$import_frontpage   = $this->app->request->getBool('import-frontpage', false);
		$import_categories  = $this->app->request->getBool('import-categories', false);
		$element_assignment = $this->app->request->get('element-assign', 'array', array());
		$types				= $this->app->request->get('types', 'array', array());
		$file 				= $this->app->request->getCmd('file', '');
		$file 				= rtrim($this->app->system->application->getCfg('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {
			$this->app->import->import($file, $import_frontpage, $import_categories, $element_assignment, $types);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}

	public function doImportCSV() {

		// init vars
		$contains_headers   = $this->app->request->getBool('contains-headers', false);
		$field_separator    = $this->app->request->getString('field-separator', ',');
		$field_enclosure    = $this->app->request->getString('field-enclosure', '"');
		$element_assignment = $this->app->request->get('element-assign', 'array', array());
		$type				= $this->app->request->getCmd('type', '');
		$file 				= $this->app->request->getCmd('file', '');
		$file 				= rtrim($this->app->system->application->getCfg('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {
			$this->app->import->importCSV($file, $type, $contains_headers, $field_separator, $field_enclosure, $element_assignment);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}

	public function doExport() {

		$exporter = $this->app->request->getString('exporter');

		if ($exporter) {

			try {

				// set_time_limit doesn't work in safe mode
		        if (!ini_get('safe_mode')) {
				    @set_time_limit(0);
		        }

				$json = $this->app->export->create($exporter)->export();

				header("Pragma: public");
		        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		        header("Expires: 0");
		        header("Content-Transfer-Encoding: binary");
				header ("Content-Type: application/json");
				header('Content-Disposition: attachment;'
				.' filename="'.JFilterOutput::stringURLSafe($this->application->name).'.json";'
				);

				echo $json;

			} catch (AppExporterException $e) {

				// raise error on exception
				$this->app->error->raiseNotice(0, JText::_('Error Exporting').' ('.$e.')');
				$this->setRedirect($this->baseurl.'&task=importexport', $msg);

			}
		}
	}

}

/*
	Class: ConfigurationControllerException
*/
class ConfigurationControllerException extends AppException {}