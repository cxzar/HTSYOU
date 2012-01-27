<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SubmissionController
		Site submission controller class
*/
class SubmissionController extends AppController {

	/*
       Class constants
    */
	const SESSION_PREFIX   = 'ZOO_';
    const PAGINATION_LIMIT = 20;
	const TIME_BETWEEN_PUBLIC_SUBMISSIONS = 300;
	const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';
	const CALENDAR_DATE_FORMAT = '%Y-%m-%d';

    /*
       Variable: submission
         Current submission.
    */
    public $application;
	public $submission;
    public $type;
    public $item_id;
    public $item;
    public $renderer;
    public $layout;
    public $layout_path;
	public $session_form_key;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// get user
		$this->user = $this->app->user->get();

		// get item id
        $this->item_id = $this->app->request->getInt('item_id');

		// get pathway
		$this->pathway = $this->app->system->application->getPathway();

        // get submission info from Request
        if (!$submission_id = $this->app->request->getInt('submission_id')) {

            // else get submission info from menu item
            if ($menu = $this->app->object->create('JSite')->getMenu()->getActive()) {

                $this->menu_params   = $this->app->parameter->create($menu->params);
                $submission_id = $this->menu_params->get('submission');
            }
        }

        // set submission
        if ($this->submission  = $this->app->table->submission->get((int) $submission_id)) {

            // set application
            $this->application = $this->submission->getApplication();

            // set template
            $this->template    = $this->application->getTemplate();

			// set session form key
			$this->session_form_key = self::SESSION_PREFIX . 'SUBMISSION_FORM_' . $this->submission->id;

        }

		// load administration language files
		$this->app->system->language->load('', JPATH_ADMINISTRATOR, null, true);
		$this->app->system->language->load('com_zoo', JPATH_ADMINISTRATOR, null, true);

	}

    public function mysubmissions() {

        try {

            $this->_checkConfig();

			if (!$this->app->user->canAccess($this->user, 1)) {
				throw new SubmissionControllerException('Insufficient User Rights.');
			}

			// get request vars
			$order = $this->app->request->getCmd('order', $this->app->system->application->getParams()->get('order', 0));

            $limit = SubmissionController::PAGINATION_LIMIT;
            $state_prefix      = $this->option.'_'.$this->application->id.'.submission.'.$this->submission->id;
            $this->filter_type = $this->app->system->application->getUserStateFromRequest($state_prefix.'.filter_type', 'filter_type', '', 'string');
			$search	           = $this->app->system->application->getUserStateFromRequest($state_prefix.'.search', 'search', '', 'string');
			$search			   = $this->app->string->strtolower($search);
            $page              = $this->app->system->application->getUserStateFromRequest($state_prefix.'.page', 'page', 1, 'int');

            $limitstart = ($page - 1) * $limit;

            $table = $this->app->table->item;

            $this->types = $this->submission->getSubmittableTypes();

            // set renderer
            $this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

			// select
			$select = 'a.*';

			// get from
			$from = $table->name.' AS a';

            // get data from the table
            $where = array();

            // application filter
            $where[] = 'application_id = ' . (int) $this->application->id;

            // type filter
            if (empty($this->filter_type)) {
                $where[] = 'type IN ("' . implode('", "', array_keys($this->types)) . '")';
            } else {
                $where[] = 'type = "' . (string) $this->filter_type . '"';
            }

			if ($search) {
				$from   .= ' LEFT JOIN '.ZOO_TABLE_TAG.' AS t ON a.id = t.item_id';
				$where[] = 'LOWER(a.name) LIKE '.$this->app->database->Quote('%'.$this->app->database->getEscaped($search, true).'%', false)
					. ' OR LOWER(t.name) LIKE '.$this->app->database->Quote('%'.$this->app->database->getEscaped($search, true).'%', false);
			}

            // author filter
            $where[] = 'created_by = ' . $this->user->id;

            // user rights
            $where[] = $this->app->user->getDBAccessString($this->user);

			// conditions
			$conditions = array(implode(' AND ', $where));

			// order
			$orders = array(
				'date'   => 'a.created ASC',
				'rdate'  => 'a.created DESC',
				'alpha'  => 'a.name ASC',
				'ralpha' => 'a.name DESC',
				'hits'   => 'a.hits DESC',
				'rhits'  => 'a.hits ASC');

			$order = isset($orders[$order]) ? $orders[$order] : $orders['rdate'];

            $options          = compact('select', 'from', 'conditions', 'order');
            $this->items      = $table->all($limit ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
            $this->pagination = $this->app->pagination->create($table->count($options), $page, $limit, 'page', 'app');

            // type select
			if (count($this->types) > 1) {
				$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Type').' -'));
				foreach ($this->types as $id => $type) {
					$options[] = $this->app->html->_('select.option', $id, $type->name);
				}
				$this->lists['select_type'] = $this->app->html->_('select.genericlist', $options, 'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $this->filter_type);
			}

			// add search
			$this->lists['search'] = $search;

            // display view
            $this->getView('submission')->addTemplatePath($this->template->getPath())->setLayout('mysubmissions')->display();

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            $this->app->error->raiseWarning(0, (string) $e);

        }
    }

    public function submission() {

        try {

            $this->_init();

            // is current user the item owner and does the user have sufficient user rights
            if ($this->item->id && (!$this->item->canAccess($this->user) || $this->item->created_by != $this->user->id)) {
                throw new AppControllerException('You are not allowed to edit this item.');
            }

			// build new form
			$this->form = $this->app->form->create(array('submission' => $this->submission, 'item' => $this->item, 'elements_config' => $this->elements_config))
				->setIgnoreErrors(true)
				->bindItem();

			// bind form from sessions post data
			if ($post = unserialize($this->app->system->application->getUserState($this->session_form_key))) {

				// remove form from session
				$this->app->system->application->setUserState($this->session_form_key, null);

				// bind post to form
				$this->form
					->setIgnoreErrors(false)
					->bind($post);
			}

            // build cancel url
            if (!empty($this->redirectTo)) {
                $this->cancel_url = $this->app->route->mysubmissions($this->submission);
				$text = $this->item->id ? JText::_('Edit Submission') : JText::_('Add Submission');
				$this->pathway->addItem($text);
            }

            if ($this->submission->isInTrustedMode()) {
                // most used tags
                $this->lists['most_used_tags'] = $this->app->table->tag->getAll($this->application->id, null, null, 'items DESC, a.name ASC', null, 8);
            }

            // display view
            $this->getView('submission')->addTemplatePath($this->template->getPath())->setLayout('submission')->display();

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            $this->app->error->raiseWarning(0, (string) $e);

        }

    }

    public function save() {

        // check for request forgeries
        $this->app->request->checkToken() or jexit('Invalid Token');

        // init vars
        $post		= $this->app->request->get('post:', 'array');
		$db		    = $this->app->database;
		$tzoffset   = $this->app->date->getOffset();
		$now	    = $this->app->date->create();
		$now->setOffset($tzoffset);
		$msg	    = '';
		$time_valid = true;

        try {

            $this->_init();

			// is this an item edit?
			$edit = (bool) $this->item->id;

            // is current user the item owner and does the user have sufficient user rights
            if ($edit && (!$this->item->canAccess($this->user) || $this->item->created_by != $this->user->id)) {
                throw new AppControllerException('You are not allowed to make changes to this item.');
            }

            // get default category - only in none trusted mode
            $categories = array();
            if (!$this->submission->isInTrustedMode() && ($category = $this->submission->getForm($this->type->id)->get('category'))) {
                $categories[] = $category;
            }

            // get element data from post
            if (isset($post['elements'])) {

                // filter element data
                if (!$this->submission->isInTrustedMode() && !$this->app->user->isJoomlaAdmin($this->user)) {
                    $this->app->request->setVar('elements', $this->app->submission->filterData($post['elements']));
                    $post = $this->app->request->get('post:', 'array');
                }

                // merge elements into post
                $post = array_merge($post, $post['elements']);
            }

			// merge userfiles element data with post data
			foreach ($_FILES as $key => $userfile) {
				if (strpos($key, 'elements_') === 0) {
					$post[str_replace('elements_', '', $key)]['userfile'] = $userfile;
				}
			}

			// fix publishing dates in trusted mode
			if ($this->submission->isInTrustedMode()) {

				// set publish up date
				if (isset($post['publish_up']) && empty($post['publish_up'])) {
					$post['publish_up'] = $now->toMySQL(true);
				}

				// set publish down date
				if (isset($post['publish_down']) && (trim($post['publish_down']) == JText::_('Never') || trim($post['publish_down']) == '')) {
					$post['publish_down'] = $db->getNullDate();
				}
			}

            // sanatize tags
            if (!isset($post['tags'])) {
                $post['tags'] = array();
            }

            // build new item form and bind it with post data
            $form = $this->app->form->create(array('submission' => $this->submission, 'item' => $this->item, 'elements_config' => $this->elements_config))
				->bind($post);

			// save item if form is valid
            if ($form->isValid()) {

                // set name
				$name_changed = $form->getValue('name') != $this->item->name;
                $this->item->name = $form->getValue('name');

                // bind elements
                foreach ($this->elements_config as $data) {
                    if (($element = $this->item->getElement($data['element'])) && $field = $form->getFormField($data['element'])) {
                        if ($field_data = $field->hasError() ? $field->getTaintedValue() : $field->getValue()) {
                            $element->bindData($field_data);
                        } else {
                            $element->bindData();
                        }

                        // perform submission uploads
                        if ($element instanceof iSubmissionUpload) {
                            $element->doUpload();
                        }
                    }
                }

                // set alias
				if (!$edit || $name_changed) {
					$this->item->alias = $this->app->alias->item->getUniqueAlias($this->item->id, $this->app->string->sluggify($this->item->name));
				}

                // set modified
                $this->item->modified	 = $now->toMySQL();
                $this->item->modified_by = $this->user->get('id');

				// creating new item
                if (!$edit) {

					// set created date
                    $this->item->created		  = $now->toMySQL();
                    $this->item->created_by		  = $this->user->get('id');
					$this->item->created_by_alias = '';

					// set publish up - publish down
					$this->item->publish_up   = $now->toMySQL();
					$this->item->publish_down = $db->getNullDate();

					// set access
					$this->item->access = $this->app->joomla->getDefaultAccess();

					// set searchable
					$this->item->searchable = 1;

					// set params
					$this->item->getParams()
						->set('config.enable_comments', true)
						->set('config.primary_category', 0);

                }

                if ($this->submission->isInTrustedMode()) {

                    // set state
                    $this->item->state = $form->getValue('state');

					// set state
                    $this->item->access = $form->getValue('access');

					// set publish up
					if (($this->item->publish_up = $form->getValue('publish_up')) && !empty($this->item->publish_up)) {
						$this->item->publish_up = $this->app->date->create($this->item->publish_up, $tzoffset)->toMySQL();
					}

					// set publish down
					if (($this->item->publish_down = $form->getValue('publish_down')) && !empty($this->item->publish_down) && !($this->item->publish_down == $db->getNullDate())) {
						$this->item->publish_down = $this->app->date->create($this->item->publish_down, $tzoffset)->toMySQL();
					}

                    // set searchable
                    $this->item->searchable = $form->getValue('searchable');

                    // set comments enabled
                    $this->item->getParams()
                        ->set('config.enable_comments', $form->getValue('enable_comments'));

                    // set frontpage
                    if ($form->getValue('frontpage')) {
                        $categories[] = 0;
                    }

                    // set categories
					$tmp_categories = $form->getValue('categories');
					if (!empty($tmp_categories)) {
						foreach ($form->getValue('categories') as $category) {
							$categories[] = $category;
						}
					}

                    // set tags
                    $tags = $form->hasError('tags') ? $form->getTaintedValue('tags') : $form->getValue('tags');
                    $this->item->setTags($tags);

                } else {

					// spam protection - user may only submit items every SubmissionController::TIME_BETWEEN_PUBLIC_SUBMISSIONS seconds
					if (!$edit) {
						$timestamp = $this->app->system->session->get('ZOO_LAST_SUBMISSION_TIMESTAMP');
						$now = time();
						if ($now < $timestamp + SubmissionController::TIME_BETWEEN_PUBLIC_SUBMISSIONS) {

							$this->app->system->application->setUserState($this->session_form_key, serialize($post));
							$time_valid = false;

							throw new SubmissionControllerException('You are submitting too fast, please try again in a few moments.');
						}
						$this->app->system->session->set('ZOO_LAST_SUBMISSION_TIMESTAMP', $now);
					}

					// set state
					$this->item->state = 0;

				}

                // save item
                $this->app->table->item->save($this->item);

                // save category relations - only if editing in trusted mode
				if (!$edit || $this->submission->isInTrustedMode()) {
					$this->app->category->saveCategoryItemRelations($this->item->id, $categories);
				}

                // set redirect message
				$msg = $this->submission->isInTrustedMode() ? JText::_('Thanks for your submission.') : JText::_('Thanks for your submission. It will be reviewed before being posted on the site.');

				// trigger saved event
				$this->app->event->dispatcher->notify($this->app->event->create($this->submission, 'submission:saved', array('item' => $this->item, 'new' => !$edit)));

			// add form to session if form is not valid
            } else {

				$this->app->system->application->setUserState($this->session_form_key, serialize($post));

            }

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            $this->app->error->raiseWarning(0, (string) $e);

        } catch (AppException $e) {

            // raise warning on exception
            $this->app->error->raiseWarning(0, JText::_('There was an error saving your submission, please try again later.'));

            // add exception details, for super administrators only
            if ($this->user->superadmin) {
                $this->app->error->raiseWarning(0, (string) $e);
            }

        }

        // redirect to mysubmissions
		$link = '';
        if ($this->redirectTo == 'mysubmissions' && $form && $form->isValid() && $time_valid) {
            $link = $this->app->route->mysubmissions($this->submission);
        // redirect to edit form
        } else {
			$link = $this->app->route->submission($this->submission, $this->type->id, $this->hash, $this->item_id, $this->redirectTo);
        }

        $this->setRedirect(JRoute::_($link, false), $msg);
    }

    public function remove() {

        // init vars
        $msg = null;

        try {

            $this->_checkConfig();

            if (!$this->submission->isInTrustedMode()) {
                throw new AppControllerException('The submission is not in Trusted Mode.');
            }

			// get item table and delete item
			$table = $this->app->table->item;

            $item = $table->get($this->item_id);

            // is current user the item owner and does the user have sufficient user rights
            if ($item->id && (!$item->canAccess($this->user) || $item->created_by != $this->user->id)) {
                throw new AppControllerException('You are not allowed to make changes to this item.');
            }

            $table->delete($item);

			// set redirect message
			$msg = JText::_('Submission Deleted');

			// trigger deleted event
			$this->app->event->dispatcher->notify($this->app->event->create($item, 'submission:deleted'));

		} catch (AppException $e) {

            // raise warning on exception
            $this->app->error->raiseWarning(0, JText::_('There was an error deleting your submission, please try again later.'));

            // add exception details, for super administrators only
            if ($this->user->superadmin) {
                $this->app->error->raiseWarning(0, (string) $e);
            }

		}

        $this->setRedirect(JRoute::_($this->app->route->mysubmissions($this->submission), false), $msg);

    }

	public function loadtags() {

		// get request vars
		$tag = $this->app->request->getString('tag', '');

		echo $this->app->tag->loadTags($this->application->id, $tag);
	}

    protected function _checkConfig() {

        if (!$this->application || !$this->submission) {
            throw new SubmissionControllerException('Submissions are not configured correctly.');
        }

        if (!$this->submission->getState()) {
            throw new SubmissionControllerException('Submissions are disabled.');
        }

        if (!$this->submission->canAccess($this->user)) {
            throw new SubmissionControllerException('Insufficient User Rights.');
        }
    }

    protected function _init() {

        //init vars
        $type_id        = $this->app->request->getCmd('type_id');
        $hash           = $this->app->request->getCmd('submission_hash');
        $this->redirectTo = $this->app->request->getString('redirect');

        // check config
        $this->_checkConfig();

        // get submission info from request
        if ($type_id) {

            if ($hash != $this->app->submission->getSubmissionHash($this->submission->id, $type_id, $this->item_id)) {
                throw new SubmissionControllerException('Hashes did not match.');
            }

        // else get submission info from active menu
        } elseif ($this->menu_params) {
            $type_id = $this->menu_params->get('type');

            // remove item_id (menu item may not have an item_id)
            $this->item_id = null;
        }

        // set type
        $this->type  = $this->submission->getType($type_id);

        // check type
        if (!$this->type) {
            throw new SubmissionControllerException('Submissions are not configured correctly.');
        }

        // set hash
        $this->hash = $hash ? $hash : $this->app->submission->getSubmissionHash($this->submission->id, $this->type->id, $this->item_id);

        // set layout
        $this->layout = $this->submission->getForm($this->type->id)->get('layout', '');

        // check layout
        if (empty($this->layout)) {
            throw new SubmissionControllerException('Submission is not configured correctly.');
        }

		// set renderer
		$this->renderer = $this->app->renderer->create('submission')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

        // set layout path
        $this->layout_path = 'item.';
        if ($this->renderer->pathExists('item/'.$this->type->id)) {
                $this->layout_path .= $this->type->id.'.';
        }
        $this->layout_path .= $this->layout;

        // get positions
        $positions = $this->renderer->getConfig('item')->get($this->application->getGroup().'.'.$this->type->id.'.'.$this->layout, array());

        // get elements from positions
        $this->elements_config = array();
        foreach ($positions as $position) {
            foreach ($position as $element) {
				if ($element_obj = $this->type->getElement($element['element'])) {
					if (!$this->submission->isInTrustedMode()) {
						if ($element_obj->getMetaData('trusted') == 'true') {
							continue;
						}
					}

					$this->elements_config[$element['element']] = $element;
				}
            }
        }

        // get item table
        $table = $this->app->table->item;

        // get item
		if (!$this->item_id || !($this->item = $table->get($this->item_id))) {
            $this->item = $this->app->object->create('Item');
            $this->item->application_id = $this->application->id;
            $this->item->type = $this->type->id;
			$this->item->publish_up = $this->app->date->create()->toMySQL();
			$this->item->publish_down = $this->app->database->getNullDate();
			$this->item->access = $this->app->joomla->getDefaultAccess();
        }

    }

}

/*
	Class: SubmissionControllerException
*/
class SubmissionControllerException extends AppException {}