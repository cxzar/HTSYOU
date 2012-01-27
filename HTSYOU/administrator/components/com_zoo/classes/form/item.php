<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ItemForm
		The class for validating item submissions.
*/
class ItemForm extends AppForm {

    protected $_item;
    protected $_submission;
    protected $_elements_config = array();

    public function config($args = array()) {

        parent::config($args);

        // set item
        $this->_item = isset($args['item']) ? $args['item'] : null;

        // set submission
        $this->_submission = isset($args['submission']) ? $args['submission'] : null;

        // set elements
        $this->_elements_config = isset($args['elements_config']) ? $args['elements_config'] : array();

        // set submission form fields
        $this->setFormFields(
            array(
                'name'            => new AppFormField('name', $this->app->validator->create('string')),
                'state'           => new AppFormField('state', $this->app->validator->create('', array('required' => false))),
				'publish_up'      => new AppFormField('publish_up', $this->app->validator->create('date', array('required' => false, 'allow_db_null_date' => true))),
				'publish_down'    => new AppFormField('publish_down', $this->app->validator->create('date', array('required' => false, 'allow_db_null_date' => true))),
                'searchable'      => new AppFormField('searchable', $this->app->validator->create('', array('required' => false))),
                'enable_comments' => new AppFormField('enable_comments', $this->app->validator->create('', array('required' => false))),
                'frontpage'       => new AppFormField('frontpage', $this->app->validator->create('', array('required' => false))),
                'categories'      => new AppFormField('categories', $this->app->validator->create('foreach', $this->app->validator->create('integer'), array('required' => false))),
				'access'	      => new AppFormField('access', $this->app->validator->create('integer', array('required' => false))),
                'tags'            => new AppFormField('tags', $this->app->validator->create('foreach', $this->app->validator->create('string'), array('required' => false)))
            )
        );

        foreach (array_keys($this->_elements_config) as $identifier) {
            $this->addFormField(new AppFormFieldElement($identifier, $this));
        }

    }

    public function bindItem() {
        $data = array();
        if ($this->_item) {

			$null = $this->app->database->getNullDate();

            $data['name']   = $this->_item->name;
            $data['state']  = $this->_item->state;
			$data['access'] = $this->_item->access;

			$data['publish_up'] = $this->_item->publish_up;
			if ($data['publish_up'] != $null) {
				$data['publish_up'] = $this->app->html->_('date', $data['publish_up'], $this->app->date->format('%Y-%m-%d %H:%M:%S'), $this->app->date->getOffset());
			}

			$data['publish_down'] = $this->_item->publish_down;
			if ($data['publish_down'] != $null) {
				$data['publish_down'] = $this->app->html->_('date', $data['publish_down'], $this->app->date->format('%Y-%m-%d %H:%M:%S'), $this->app->date->getOffset());
			}

            $data['searchable']      = $this->_item->searchable;
            $data['enable_comments'] = $this->_item->isCommentsEnabled();
			$data['categories']      = $this->_item->getRelatedCategoryIds();
            $data['frontpage']       = in_array(0, $data['categories']);
            $data['tags']            = $this->_item->getTags();

            foreach (array_keys($this->_elements_config) as $identifier) {
                if ($element = $this->_item->getElement($identifier)) {
					if ($element instanceof ElementDate) {
						foreach ($element as $index => $self) {
							$value = $element->get('value');
							if (!empty($value)) {
								$value = $this->app->html->_('date', $value, $this->app->date->format('%Y-%m-%d %H:%M:%S'), $this->app->date->getOffset());
							}
							$data[$identifier][$index]['value'] = $value;
						}
					} else {
						$data[$identifier] = $element->data();
					}
				}
            }
        }

		return parent::bind($data);
    }

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function getElementConfig($identifier) {
        if (isset($this->_elements_config[$identifier])) {
            return (array) $this->_elements_config[$identifier];
        }
        return array();
    }

    public function setSubmission($submission) {
        $this->_submission = $submission;
        return $this;
    }

    public function getSubmission() {
        return $this->_submission;
    }

}

/*
	Class: AppFormFieldElement
		The AppFormFieldElement class.
*/
class AppFormFieldElement extends AppFormField {

    protected $_form;

    public function __construct($name, $form) {
        parent::__construct($name);
        $this->_form = $form;
    }

    public function bind($value) {

        // bind unmodified value
        $this->_tainted_value = $value;

        try {

			// get element
			if ($element = $this->_form->getItem()->getElement($this->_name)) {

				// get params
				$params = $this->app->data->create(array_merge(array('trusted_mode' => $this->_form->getSubmission()->isInTrustedMode()), $this->_form->getElementConfig($this->_name)));

				// get AppData value
				$value = $this->app->data->create($value);

				// validate the element
				$this->_value = $this->_ignore_errors ? $value : $element->validateSubmission($value, $params);

			}

        } catch (AppValidatorException $e) {

            $this->setError($e);

        }

    }

}