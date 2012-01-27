<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Application
		Application related attributes and functions.
*/
class Application {

    /*
		Variable: id
			Primary key.
    */
	public $id;

    /*
		Variable: name
			Application name.
    */
	public $name;

    /*
		Variable: alias
			Application alias.
    */
	public $alias;

    /*
		Variable: description
			Application description.
    */
	public $description;

    /*
		Variable: application_group
			Application group.
    */
	public $application_group;

    /*
       Variable: params
         Application params.
    */
	public $params;

    /*
		Variable: metaxml_file
			Application meta xml filename.
    */
	public $metaxml_file = 'application.xml';

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
       Variable: _categories
         Related categories.
    */
	public $_categories;

    /*
       Variable: _category_tree
         Related categories tree.
    */
	public $_category_tree;

	/*
		Variable: _metaxml
			Application meta data SimpleXMLElement.
    */
	public $_metaxml;

	/*
		Variable: _submissions
			Application submission instances.
    */
	protected $_submissions = array();

	/*
		Variable: _types
			Application types instances.
    */
	protected $_types = array();

	/*
		Variable: _templates
			Application template instances.
    */
	protected $_templates = array();

	/*
		Variable: _elements_path_registered
			Is the elements path already registered?
    */
	protected $_elements_path_registered = false;

 	/*
		Function: Constructor

		Returns:
			Order
	*/
	public function __construct() {

		// init vars
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);

	}

	/*
		Function: dispatch
			Dispatch application through executing the current controller.

		Returns:
			Void
	*/
	public function dispatch() {
		// delegate dispatch
		$this->app->dispatch('default');
	}

	/*
		Function: getPath
			Retrieve application path.

		Returns:
			String - Application path
	*/
	public function getPath() {
		return $this->app->path->path($this->getResource());
	}

	/*
		Function: getResource
			Returns the applications resource

		Returns:
			String - applications element resource
	*/
	public function getResource() {
		return "applications:{$this->getGroup()}/";
	}

	/*
		Function: getURI
			Retrieve application URI.

		Returns:
			String - Application URI
	*/
	public function getURI() {
		return $this->app->path->url($this->getResource());
	}

	/*
		Function: hasIcon
			Checks for icon existence.

		Returns:
			Boolean - true if icon exists
	*/
	public function hasIcon() {
		return (bool) $this->app->path->path($this->getResource() . 'application.png');
	}

	/*
		Function: getIcon
			Retrieve application icon.

		Returns:
			String - icon URI
	*/
	public function getIcon() {
		if ($this->hasIcon()) {
			return $this->app->path->url($this->getResource() . 'application.png');
		} else {
			return $this->app->path->url('assets:images/zoo.png');
		}
	}

	/*
		Function: getInfoImage
			Retrieve application info image.

		Returns:
			String - icon URI
	*/
	public function getInfoImage() {
		if ($this->app->path->path($this->getResource() . 'application_info.png')) {
			return $this->app->path->url($this->getResource() . 'application_info.png');
		}
		return '';
	}

	/*
		Function: getToolbarTitle
			Retrieve applications toolbar title html.

		Returns:
			String - toolbar title html
	*/
	public function getToolbarTitle($title) {

		$html[] = '<div class="header icon-48-'.(($this->hasIcon()) ? 'application"' : 'zoo"').'>';
		$html[] = $this->hasIcon() ? '<img src="'.$this->getIcon().'" width="48" height="48" style="margin-left:-55px;vertical-align:middle;" />' : null;
		$html[] = $title;
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/*
		Function: getGroup
			Retrieve application group.

		Returns:
			String - Application group
	*/
	public function getGroup() {
		return $this->application_group;
	}

	/*
		Function: setGroup
			Set application group.

		Returns:
			Application
	*/
	public function setGroup($group) {
		$this->application_group = $group;
		return $this;
	}

	/*
    	Function: getCategories
    	  Get categories.

		Parameters:
	      $published - If true, return only published categories.

	   Returns:
	      Array - Categories
 	*/
	public function getCategories($published = false, $item_count = false) {

		// get categories
		if (empty($this->_categories)) {
			$this->_categories = $this->app->table->category->getAll($this->id, $published, $item_count);
		}

		return $this->_categories;
	}

	/*
    	Function: getCategoryTree
    	  Get categories as tree.

		Parameters:
	      $published - If true, return only published categories.
	      $user - User

	   Returns:
	      Array - Categories
 	*/
	public function getCategoryTree($published = false, $user = null, $item_count = false) {

		// get category tree
		if (empty($this->_category_tree)) {
			// get categories and item count
			$categories = $this->getCategories($published, $item_count);

			$this->_category_tree = $this->app->tree->build($categories, 'Category');
			$this->_category_tree[0]->application_id = $this->id;
		}

		return $this->_category_tree;
	}

	/*
    	Function: getCategoryCount
    	  Get categories count.

	   Returns:
	      Int
 	*/
	public function getCategoryCount() {
		return $this->app->table->category->count(array('conditions' => array('application_id=?',$this->id)));
	}

	/*
    	Function: getItemCount
    	  Get item count.

	   Returns:
	      Int
 	*/
	public function getItemCount() {
		return $this->app->table->item->getApplicationItemCount($this->id);
	}

	/*
		Function: getTemplate
			Get active application template.

		Returns:
			Template
	*/
	public function getTemplate() {
		$templates = $this->getTemplates();
		if (($name = $this->getParams()->get('template')) && isset($templates[$name])) {
			return $templates[$name];
		}

		return null;
	}

	/*
		Function: getTemplates
			Get application templates.

		Returns:
			Array
	*/
	public function getTemplates() {

		if (empty($this->_templates)) {
			if ($folders = $this->app->path->dirs($this->getResource().'templates')) {
				foreach ($folders as $folder) {
					$this->_templates[$folder] = $this->app->template->create(array($folder, $this->getResource().'templates/'.$folder));
				}
			}
		}

		return $this->_templates;
	}

	/*
		Function: getType
			Retrieve application type by id.

		Parameters:
  			id - Type id.

		Returns:
			Type
	*/
	public function getType($id) {
		$types = $this->getTypes();

		if (isset($types[$id])) {
			return $types[$id];
		}

		return null;
	}

	/*
		Function: getTypes
			Retrieve application types.

		Returns:
			Array
	*/
	public function getTypes() {

		if (empty($this->_types)) {

			$this->_types = array();
			$path   = $this->getResource() . '/types';
			$filter = '/^.*config$/';

			if ($files = $this->app->path->files($path, false, $filter)) {
				foreach ($files as $file) {
					$alias = basename($file, '.config');
					$this->_types[$alias] = $this->app->object->create('Type', array($alias, $this));
				}
			}
		}

		return $this->_types;
	}

  	/*
		Function: getSubmission
			Retrieve application submission by id.

		Parameters:
  			id - Submission id.

		Returns:
			Submission
	*/
	public function getSubmission($id) {
		$submissions = $this->getSubmissions();

		if (isset($submissions[$id])) {
			return $submissions[$id];
		}

		return null;
	}

	/*
		Function: getSubmissions
			Retrieve application submissions.

		Returns:
			Array
	*/
	public function getSubmissions() {

		if (empty($this->_submissions)) {
            $this->_submissions = $this->app->table->submission->all(array('conditions' => array('application_id = ' . (int) $this->id)));
		}

		return $this->_submissions;
	}

	/*
		Function: getParams
			Gets application params.

		Parameters:
  			$for - Get params for a specific use, including overidden values.

		Returns:
			ParameterData - params
	*/
	public function getParams($for = null) {

		// get site params
		if ($for == 'site') {

			return $this->app->parameter->create()
				->loadArray($this->params)
				->set('config.', $this->params->get('global.config.'))
				->set('template.', $this->params->get('global.template.'));

		// get frontpage params and inherit globals
		} elseif ($for == 'frontpage') {

			return $this->app->parameter->create()
				->set('config.', $this->params->get('global.config.'))
				->set('template.', $this->params->get('global.template.'))
				->loadArray($this->params);
		}

		return $this->params;
	}

	/*
		Function: getParamsForm
			Gets application params form.

		Returns:
			AppParameterForm
	*/
	public function getParamsForm() {

		// get parameter xml file
		if ($xml = $this->app->path->path($this->getResource().$this->metaxml_file)) {

			// get form
			return $this->app->parameterform->create($xml);

		}

		return null;
	}

	/*
		Function: getAddonParamsForm
			Gets application addon params form.

		Returns:
			AppParameterForm
	*/
	public function getAddonParamsForms() {

		$forms = array();

		// load xml config files
		foreach ($this->app->path->files($this->getResource() . 'config/', false, '/\.xml$/i') as $file) {
			if (($file = $this->app->path->path($this->getResource() . 'config/' . $file)) && ($xml = $this->app->xml->loadFile($file))) {
				if ($xml->getName() == 'config') {
					$forms[(string) $xml->name] = $this->app->parameterform->create($file);
				}
			}
		}

		return $forms;
	}

	/*
		Function: getMetaData
			Get application xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData($key = null) {

		$data = $this->app->data->create();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}

		if ($xml->getName() != 'application') {
			return false;
		}

		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;
		$data['license']  	  = (string) $xml->license;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		return $key == null ? $data : $data->get($key);

	}

	/*
		Function: getMetaXML
			Get application xml meta file.

		Returns:
			Object - SimpleXMLElement
	*/
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = $this->app->xml->loadFile($this->getMetaXMLFile());
		}

		return $this->_metaxml;
	}

	/*
		Function: getMetaXMLFile
			Get application path to xml meta file.

		Returns:
			String
	*/
	public function getMetaXMLFile() {
		return $this->getPath() . '/' . $this->metaxml_file;
	}

	/*
		Function: getImage
		  Get image resource info.

		Parameters:
	      $name - the param name of the image

	   Returns:
	      Array - Image info
	*/
	public function getImage($name) {
		$params = $this->getParams();
		if ($image = $params->get($name)) {

			return $this->app->html->_('zoo.image', $image, $params->get($name . '_width'), $params->get($name . '_height'));

		}
		return null;
	}

	/*
		Function: getImage
		  Executes Content Plugins on text.

		Parameters:
	      $text - the text

	   Returns:
	      text - string
	*/
	public function getText($text) {
		return $this->app->zoo->triggerContentPlugins($text, array(), 'com_zoo.application.description');
	}

	/*
		Function: addMenuItems
		  Add menu items of application in administrator menu.

	   Returns:
	      Void
	*/
	public function addMenuItems($menu) {

		// get current controller
		$controller = $this->app->request->getWord('controller');
		$controller = in_array($controller, array('new', 'manager')) ? 'item' : $controller;

		// create application tab
		$tab = $this->app->object->create('AppMenuItem', array($this->id, $this->name, $this->app->link(array('controller' => $controller, 'changeapp' => $this->id))));
		$menu->addChild($tab);

		// menu items
		$items = array(
			'item'          => JText::_('Items'),
			'category'      => JText::_('Categories'),
			'frontpage'     => JText::_('Frontpage'),
			'comment'       => JText::_('Comments'),
			'tag'           => JText::_('Tags'),
            'submission'    => JText::_('Submissions')
		);

		// add menu items
		foreach ($items as $controller => $name) {
			$tab->addChild($this->app->object->create('AppMenuItem', array($this->id.'-'.$controller, $name, $this->app->link(array('controller' => $controller, 'changeapp' => $this->id)))));
		}

		// add config menu item
		$id     = $this->id.'-configuration';
		$link   = $this->app->link(array('controller' => 'configuration', 'changeapp' => $this->id));
		$config = $this->app->object->create('AppMenuItem', array($id, JText::_('Config'), $link));
		$config->addChild($this->app->object->create('AppMenuItem', array($id, JText::_('Application'), $link)));
		$config->addChild($this->app->object->create('AppMenuItem', array($id.'-importexport', JText::_('Import / Export'), $this->app->link(array('controller' => 'configuration', 'changeapp' => $this->id, 'task' => 'importexport')))));
		$tab->addChild($config);
	}

	/*
    	Function: isCommentsEnabled
    	  Checks wether comments are activated, globally and item specific.

	   Returns:
	      Boolean.
 	*/
	public function isCommentsEnabled() {
		return $this->getParams()->get('global.comments.enable_comments', 1);
	}

	/*
    	Function: registerElementsPath
			Register the applications element path.

	   Returns:
	      Application.
 	*/
	public function registerElementsPath($new = false) {
		if ($new || !$this->_elements_path_registered) {
			$this->app->path->register($this->app->path->path($this->getResource().'elements'), 'elements');
			$this->_elements_path_registered = true;
		}

		return $this;
	}

}

/*
	Class: ApplicationException
*/
class ApplicationException extends AppException {}