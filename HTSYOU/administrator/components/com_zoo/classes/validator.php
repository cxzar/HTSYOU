<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppValidator
		Validator Base Class.
*/
class AppValidator {

	public $app;

	const ERROR_CODE_REQUIRED = 100;

    protected $_messages = array();
    protected $_options  = array();

    public function __construct($options = array(), $messages = array()) {

		$this->app = App::getInstance('zoo');

        $this->_options  = array_merge(array('required' => true, 'trim' => false, 'empty_value' => null), $this->_options);
        $this->_messages = array_merge(array('required' => 'This field is required', 'invalid' => 'Invalid'), $this->_messages);

        $this->_configure($this->_options, $this->_messages);

 	    $this->_options  = array_merge($this->_options, $options);
 	    $this->_messages = array_merge($this->_messages, $messages);

    }

    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('invalid',  'Invalid');
    }

    public function clean($value) {
        $clean = $value;

        if ($this->getOption('trim') && is_string($clean)) {
            $clean = JString::trim($clean);
        }

        if ($this->isEmpty($clean)) {
            if ($this->getOption('required')) {
                throw new AppValidatorException($this->getMessage('required'), self::ERROR_CODE_REQUIRED);
            }

            return $this->getEmptyValue();
        }

        return $this->_doClean($clean);
    }

    public function addMessage($name, $value = null) {
        $this->_messages[$name] = $value;

        return $this;
    }

    public function getMessage($name) {
        return isset($this->_messages[$name]) ? $this->_messages[$name] : '';
    }

    protected function isEmpty($value) {
        return in_array($value, array(null, '', array()), true);
    }

    public function hasOption($name) {
        return isset($this->_options[$name]);
    }

    public function getOption($name) {
        if ($this->hasOption($name)) {
            return $this->_options[$name];
        }
        return null;
    }

    public function addOption($name, $value = null) {
        $this->_options[$name] = $value;
        return $this;
    }

    public function setOptions($options = array()) {
        $this->_options = $options;
        return $this;
    }

    public function getEmptyValue() {
        return $this->getOption('empty_value');
    }

    protected function _doClean($value) {
        return $value;
    }

}

/*
	Class: AppValidatorPass
		AppValidatorPass Class.
*/
class AppValidatorPass extends AppValidator {

    public function clean($value) {
        return $this->_doClean($value);
    }

    protected function _doClean($value) {
        return $value;
    }
}

/*
	Class: AppValidatorString
		AppValidatorString Class.
*/
class AppValidatorString extends AppValidator {

    protected function _doClean($value) {
        $clean = (string) $value;

        return $clean;
    }

    public function getEmptyValue() {
        return '';
    }

}

/*
	Class: AppValidatorInteger
		AppValidatorInteger Class.
*/
class AppValidatorInteger extends AppValidator {

    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not an integer.');
    }

    protected function _doClean($value) {

        $clean = intval($value);

        if (strval($clean) != $value) {
            throw new AppValidatorException($this->getMessage('number'));
        }

        return $clean;
    }

    public function getEmptyValue() {
        return 0;
    }

}

/*
	Class: AppValidatorNumber
		AppValidatorNumber Class.
*/
class AppValidatorNumber extends AppValidator {

    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not a number.');
    }

    protected function _doClean($value) {

        if (!is_numeric($value)) {
            throw new AppValidatorException($this->getMessage('number'));
        }

        $clean = floatval($value);

        return $clean;
    }

    public function getEmptyValue() {
        return 0.0;
    }

}

/*
	Class: AppValidatorFile
		AppValidatorFile Class.
*/
class AppValidatorFile extends AppValidator {

    protected function _configure($options = array(), $messages = array()) {
        if (!ini_get('file_uploads')) {
            throw new AppValidatorException('File uploads are disabled.');
        }

		$this->addOption('max_size');
        $this->addOption('mime_types');
		$this->addOption('mime_type_group');
		$this->addOption('extension');

		$this->addMessage('extension', 'This is not a valid extension.');
        $this->addMessage('max_size', 'File is too large (max %s KB).');
        $this->addMessage('mime_types', 'Invalid mime type.');
		$this->addMessage('mime_type_group', 'Invalid mime type.');
        $this->addMessage('partial', 'The uploaded file was only partially uploaded.');
        $this->addMessage('no_file', 'No file was uploaded.');
        $this->addMessage('no_tmp_dir', 'Missing a temporary folder.');
        $this->addMessage('cant_write', 'Failed to write file to disk.');
        $this->addMessage('err_extension', 'File upload stopped by extension.');

    }

    public function clean($value) {
        if (!is_array($value) || !isset($value['tmp_name'])) {
			throw new AppValidatorException($this->getMessage('invalid'));
        }

        if (!isset($value['name'])) {
			$value['name'] = '';
        }

        $value['name'] = JFile::makeSafe($value['name']);

        if (!isset($value['error'])) {
			$value['error'] = UPLOAD_ERR_OK;
        }

        if (!isset($value['size'])) {
			$value['size'] = filesize($value['tmp_name']);
        }

        if (!isset($value['type'])) {
            $value['type'] = 'application/octet-stream';
        }

        switch ($value['error']) {
			case UPLOAD_ERR_INI_SIZE:
				throw new AppValidatorException(sprintf($this->getMessage('max_size'), $this->returnBytes(@ini_get('upload_max_filesize')) / 1024), UPLOAD_ERR_INI_SIZE);
			case UPLOAD_ERR_FORM_SIZE:
				throw new AppValidatorException($this->getMessage('max_size'), UPLOAD_ERR_FORM_SIZE);
			case UPLOAD_ERR_PARTIAL:
				throw new AppValidatorException($this->getMessage('partial'), UPLOAD_ERR_PARTIAL);
			case UPLOAD_ERR_NO_FILE:
				throw new AppValidatorException($this->getMessage('no_file'), UPLOAD_ERR_NO_FILE);
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new AppValidatorException($this->getMessage('no_tmp_dir'), UPLOAD_ERR_NO_TMP_DIR);
			case UPLOAD_ERR_CANT_WRITE:
				throw new AppValidatorException($this->getMessage('cant_write'), UPLOAD_ERR_CANT_WRITE);
			case UPLOAD_ERR_EXTENSION:
				throw new AppValidatorException($this->getMessage('err_extension'), UPLOAD_ERR_EXTENSION);
        }

        // check mime type
        if ($this->hasOption('mime_types')) {
            $mime_types = $this->getOption('mime_types') ? $this->getOption('mime_types') : array();
            if (!in_array($value['type'], array_map('strtolower', $mime_types))) {
                throw new AppValidatorException($this->getMessage('mime_types'));
            }
        }

		// check mime type group
		if ($this->hasOption('mime_type_group')) {
			if (!in_array($value['type'], $this->_getGroupMimeTypes($this->getOption('mime_type_group')))) {
                throw new AppValidatorException($this->getMessage('mime_type_group'));
            }
		}

        // check file size
        if ($this->hasOption('max_size') && $this->getOption('max_size') < (int) $value['size']) {
			throw new AppValidatorException(sprintf(JTEXT::_($this->getMessage('max_size')), ($this->getOption('max_size') / 1024)));
        }

		// check extension
		if ($this->hasOption('extension') && !in_array($this->app->filesystem->getExtension($value['name']), $this->getOption('extension'))) {
			throw new AppValidatorException($this->getMessage('extension'));
        }

        return $value;
    }

	protected function _getGroupMimeTypes($group) {
		$mime_types = $this->app->data->create($this->app->filesystem->getMimeMapping());
		$mime_types = $mime_types->flattenRecursive();
		$mime_types = array_filter($mime_types, create_function('$a', 'return preg_match("/^'.$group.'\//i", $a);'));
		return array_map('strtolower', $mime_types);
	}

	protected function returnBytes ($size_str) {
	    switch (substr ($size_str, -1)) {
	        case 'M': case 'm': return (int)$size_str * 1048576;
	        case 'K': case 'k': return (int)$size_str * 1024;
	        case 'G': case 'g': return (int)$size_str * 1073741824;
	        default: return $size_str;
	    }
	}

}

/*
	Class: AppValidatorDate
		AppValidatorDate Class.
*/
class AppValidatorDate extends AppValidatorString {

    protected function _configure($options = array(), $messages = array()) {
		$this->addOption('date_format_regex', '/^((\d{2}|\d{4}))-(\d{1,2})-(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?$/');
		$this->addOption('date_format', '%Y-%m-%d %H:%M:%S');
		$this->addOption('allow_db_null_date', false);
		$this->addOption('db_null_date', $this->app->database->getNullDate());
		$this->addMessage('bad_format', '"%s" is not a valid date.');
	}

    protected function _doClean($value) {

		// init vars
		$value = parent::_doClean($value);

		if (!preg_match($this->getOption('date_format_regex'), $value)) {
			throw new AppValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		if ($this->getOption('allow_db_null_date') && $value == $this->getOption('db_null_date')) {
			return $value;
		}

		$clean = strtotime($value);

		if (empty($clean)) {
			throw new AppValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		$clean = strftime($this->getOption('date_format'), $clean);
		return $clean;

    }

}

/*
	Class: AppValidatorRegex
		AppValidatorRegex Class.
*/
abstract class AppValidatorRegex extends AppValidatorString {

    protected function _doClean($value) {

        $clean = parent::_doClean($value);

        if ($pattern = $this->getPattern()) {
            if (!preg_match($pattern, $clean)) {
                throw new AppValidatorException($this->getMessage('pattern'));
            }
        }

        return $clean;
    }

    public function setPattern($pattern) {
        $this->addOption('pattern', $pattern);
        return $this;
    }

    public function getPattern() {
        return $this->getOption('pattern');
    }

}

/*
	Class: AppValidatorEmail
		AppValidatorEmail Class.
*/
class AppValidatorEmail extends AppValidatorRegex {

    const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    protected function _configure($options = array(), $messages = array()) {
        $this->setPattern(self::REGEX_EMAIL);
        $this->addMessage('pattern', JText::_('Please enter a valid email address.'));
    }

}

/*
	Class: AppValidatorUrl
		AppValidatorUrl Class.
*/
class AppValidatorUrl extends AppValidatorRegex {

    const REGEX_URL ='/^(%s):\/\/(([a-z0-9-\\x80-\\xff]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(\/?|\/\S+)$/i';

    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('protocols', array('http', 'https', 'ftp', 'ftps'));
        $this->setPattern(sprintf(self::REGEX_URL, implode('|', $this->getOption('protocols'))));
        $this->addMessage('pattern', JText::_('Please enter a valid URL.'));
    }

}

/*
	Class: AppValidatorForeach
		The AppValidatorForeach class.
*/
class AppValidatorForeach extends AppValidator {

    protected $_validator;

    public function __construct($validator, $options = array(), $messages = array()) {

        parent::__construct($options, $messages);

        $this->_validator = $validator;

    }

    public function getValidator() {

        if (!$this->_validator) {
            $this->_validator = new AppValidatorPass();
        }

        return $this->_validator;

    }

    protected function _doClean($value) {
        $clean = array();

        if (is_array($value)) {

            foreach ($value as $key => $single_value) {
                $clean[$key] = $this->getValidator()->clean($single_value);
            }

        } else {
            throw new AppValidatorException($this->getMessage('invalid'));
        }

        return $clean;

    }

    public function getEmptyValue() {
        return array();
    }

}

/*
	Class: AppValidatorException
*/
class AppValidatorException extends AppException {

	public function __toString() {
		return JText::_($this->getMessage());
	}

}