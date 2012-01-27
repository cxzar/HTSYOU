<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: FormHelper
		Form helper class.
*/
class FormHelper extends AppHelper {

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:form'), 'form');

	}

	/*
		Function: create
			Creates a form instance

		Parameters:
			$type - Form type

		Returns:
			AppForm
	*/
	public function create($args = array(), $type = 'item') {

		$args = (array) $args;
		$class = $type . 'Form';
		$this->app->loader->register($class, 'form:'.strtolower($type).'.php');

		return $this->app->object->create($class, array($args));

	}

}

/*
	Class: AppForm
		The form class
*/
class AppForm implements ArrayAccess, Iterator, Countable {

	public $app;

    protected $_form_fields   = array();
    protected $_is_bound      = false;
    protected $_count         = 0;

    public function __construct($args = array()) {

		$this->app = App::getInstance('zoo');

		$this->config($args);
    }

    public function config($args = array()) {}

    public function getFormField($name) {
        return isset($this->_form_fields[$name]) ? $this->_form_fields[$name] : new AppFormField($name);
    }

    public function getFormFields() {
        return $this->_form_fields;
    }

    public function setFormFields($form_fields = array()) {
        foreach ($form_fields as $name => $field) {
            $this[$name] = $field;
        }
        return $this;
    }

    public function addFormField(AppFormField $form_field) {
        $this[$form_field->getName()] = $form_field;
        return $this;
    }

    public function hasFormField($name) {
        return isset($this->_form_fields[$name]);
    }

    public function bind($data) {

        if (is_array($data)) {
            foreach ($this as $name => $field) {
                $value = isset($data[$name]) ? $data[$name] : null;
                $field->bind($value);
            }
        } else if(is_object($data)) {
            foreach ($this as $name => $field) {
                $value = isset($data->$name) ? $data->$name : null;
                $field->bind($value);
            }
        }

        $this->_is_bound = true;

		return $this;

    }

    public function isBound() {
        return $this->_is_bound;
    }

    public function isValid() {
        $valid = true;
        foreach ($this as $field) {
            if ($field->hasError()) {
                $valid = false;
                break;
            }
        }
        return $this->isBound() && $valid;
    }

    public function getValue($name) {
        return isset($this[$name]) ? $this[$name]->getValue() : null;
    }

    public function getTaintedValue($name) {
        return isset($this[$name]) ? $this[$name]->getTaintedValue() : null;
    }

    public function hasError($name) {
        return isset($this[$name]) ? $this[$name]->hasError() : false;
    }

    public function getError($name) {
        return isset($this[$name]) ? $this[$name]->getError() : null;
    }

    public function getErrors() {
        $errors = array();
        foreach($this as $self) {
            if ($self->hasError()) {
                $errors[] = $self->getError();
            }
        }
        return $errors;
    }

    public function setIgnoreErrors($bool = false) {
        foreach($this as $self) {
            $self->setIgnoreErrors($bool);
        }
        return $this;
    }

    public function offsetSet($offset, $value) {
        $this->_form_fields[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->_form_fields[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_form_fields[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_form_fields[$offset]) ? $this->_form_fields[$offset] : null;
    }

    public function count() {
        return count($this->_form_fields);
    }

    public function rewind() {
        reset($this->_form_fields);

        $this->_count = count($this->_form_fields);
    }

    public function key() {
        return key($this->_form_fields);
    }

    public function current() {
        return current($this->_form_fields);
    }

    public function next() {
        next($this->_form_fields);

        --$this->_count;
    }

    public function valid() {
        return $this->_count > 0;
    }

}

/*
	Class: AppFormField
		The AppFormField class
*/
class AppFormField {

	public $app;

    protected $_name;
    protected $_tainted_value;
    protected $_value;
    protected $_validator;
    protected $_error;
    protected $_ignore_errors = false;

    public function __construct($name, $validator = null) {

		$this->app = App::getInstance('zoo');

        $this->_name = $name;
        $this->_validator = $validator ? $validator : $this->app->validator->create('pass');
    }

    public function bind($value) {

        $this->_tainted_value = $value;

        try {

            $this->_value = $this->_ignore_errors ? $value : $this->getValidator()->clean($value);

        } catch (AppValidatorException $e) {

            $this->setError($e);

        }

    }

    public function getName() {
        return $this->_name;
    }

    public function getValue() {
        return $this->_value;
    }

    public function getTaintedValue() {
        return is_string($this->_tainted_value) ? htmlspecialchars($this->_tainted_value, ENT_QUOTES, 'UTF-8') : $this->_tainted_value;
    }

    public function getValidator() {
        return $this->_validator;
    }

    public function getError() {
        return $this->_error;
    }

    public function setError(AppValidatorException $e) {
        $this->_error = $e;
    }

    public function hasError() {
        return $this->_ignore_errors ? false : count($this->_error);
    }

    public function getIgnoreErrors() {
        return $this->_ignore_errors;
    }

    public function setIgnoreErrors($bool = false) {
        $this->_ignore_errors = $bool;
        return $this;
    }

}