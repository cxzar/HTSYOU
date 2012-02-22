<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: Submission
		Submission related attributes and functions.
*/
class Submission {

    /*
       Variable: id
         Submission id.
    */
	public $id;

    /*
       Variable: identifier
         Submission unique identifier.
    */
	public $application_id;

    /*
       Variable: name
         Submission name.
    */
	public $name;

    /*
        Variable: alias
          Submission alias.
    */
	public $alias;

    /*
       Variable: state
         Submission published state.
    */
	public $state = 0;

    /*
       Variable: access
         Submission access level.
    */
	public $access;

    /*
       Variable: params
         Submission params.
    */
	public $params;

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
       Variable: _types
         Related Type objects.
    */
	protected $_types = array();

	public function  __construct() {

		// decorate data as object
		$this->params = App::getInstance('zoo')->parameter->create($this->params);

	}

	/*
    	Function: canAccess
    	  Check if item is accessible with users access rights.

	   Returns:
	      Boolean - True, if access granted
 	*/
	public function canAccess($user) {
		return $this->app->user->canAccess($user, $this->access);
	}

	/*
    	Function: getState
    	  Get published state.

	   Returns:
	      -
 	*/
	public function getState() {
		return $this->state;
	}

	/*
    	Function: setState
    	  Set published state.

	   Returns:
	      -
 	*/
	public function setState($val) {
		$this->state = $val;
	}

	/*
		Function: getParams
			Gets submission params.

		Returns:
			ParameterData - params
	*/
	public function getParams() {
		return $this->params;
	}

	/*
		Function: getTypes
			Gets submission types.

		Returns:
			array - types
	*/
    public function getTypes() {
        if (empty($this->_types)) {
            foreach (array_keys($this->params->get('form.', array())) as $type_id) {
				if ($type = $this->getApplication()->getType($type_id)) {
					$this->_types[$type_id] = $type;
				}
            }
        }

        return $this->_types;
    }

	/*
		Function: getType
			Retrieve submission type by id.

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
		Function: getSubmittableTypes
			Gets submissions submittable types.

		Returns:
			array - types
	*/
    public function getSubmittableTypes() {
        $types = $this->getTypes();
        $result = array();
        foreach ($types as $type) {
			if ($form = $this->getForm($type->id)) {
				$layout = $form->get('layout');
				if (!empty($layout)) {
					$result[$type->id] = $type;
				}
			}
        }
        return $result;
    }

	/*
		Function: getForm
			Retrieve submission parameters for a type.

		Parameters:
  			$type_id - Type id.

		Returns:
			Type
	*/
    public function getForm($type_id) {
        return $this->app->data->create($this->getParams()->get('form.'.$type_id, array()));
    }

	/*
		Function: getApplication
			Get related application object.

		Returns:
			Application - application object
	*/
	public function getApplication() {
 		return $this->app->table->application->get($this->application_id);
	}

	/*
		Function: isInTrustedMode
			Is this submission in trusted mode?

		Returns:
			Bool
	*/
    public function isInTrustedMode() {
        return (bool) $this->getParams()->get('trusted_mode', false);
    }

	/*
		Function: showTooltip
			Show tooltip?

		Returns:
			Bool
	*/
    public function showTooltip() {
        return (bool) $this->getParams()->get('show_tooltip', true);
    }

}

/*
	Class: SubmissionException
*/
class SubmissionException extends AppException {}