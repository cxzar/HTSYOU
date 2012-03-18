<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: UserAppHelper
   The Helper Class for user
*/
class UserAppHelper extends AppHelper {

	/*
		Function: getName
			Get helper name

		Returns:
			String
	*/
	public function getName() {
		return 'user';
	}

	/*
		Function: get
			Retrieve a user object
			If no identifier is supplied the user from current session is returned

		Parameters:
			$id - User identifier

		Returns:
			Mixed
	*/
	public function get($id = null) {

		// get database
		$db = $this->app->database;

		// check if user id exists
		if (!is_null($id) && !$db->queryResult('SELECT id FROM #__users WHERE id = '.$db->getEscaped($id))) {
			return null;
		}

		// get user
		$user = $this->_call(array('JFactory', 'getUser'), array($id));

		// add super administrator var to user
		$user->superadmin = $this->isJoomlaSuperAdmin($user);

		return $user;
	}

	/*
		Function: getByUsername
			Method to retrieve a user object by username.

		Parameters:
			$username - Username

		Return:
			Mixed
	*/
	public function getByUsername($username) {

		// get database
		$db = $this->app->database;

		// search username
		if ($id = $db->queryResult('SELECT id FROM #__users WHERE username = '.$db->Quote($username))) {
			return $this->get($id);
		}

		return null;
	}

	/*
		Function: getByEmail
			Method to retrieve a user object by email.

		Parameters:
			$email - User email address

		Return:
			Mixed
	*/
	public function getByEmail($email) {

		// get database
		$db = $this->app->database;

		// search email
		if ($id = $db->queryResult('SELECT id FROM #__users WHERE email = '.$db->Quote($email))) {
			return $this->get($id);
		}

		return null;
	}

	/*
		Function: getState
			Retrieve a value of a user state variable.

		Returns:
			Mixed
	*/
	public function getState($key) {
		$registry = $this->app->session->get('registry');

		if (!is_null($registry)) {
			return $registry->getValue($key);
		}

		return null;
	}

	/*
		Function: setState
			Set a value of a user state variable.

		Returns:
			Mixed
	*/
	public function setState($key, $value) {
		$registry = $this->app->session->get('registry');

		if (!is_null($registry)) {
			return $registry->setValue($key, $value);
		}

		return null;
	}

	/*
		Function: getStateFromRequest
			Retrieve a value of a user state variable.

		Returns:
			Mixed
	*/
	public function getStateFromRequest($key, $request, $default = null, $type = 'none') {

		$old = $this->getState($key);
		$cur = (!is_null($old)) ? $old : $default;
		$new = $this->app->request->getVar($request, null, 'default', $type);

		if ($new !== null) {
			$this->setState($key, $new);
		} else {
			$new = $cur;
		}

		return $new;
	}

	/*
 		Function: checkUsernameExists
 			Method to check if a username already exists.

		Parameters:
			$username - Username
			$id - User identifier

		Returns:
			Boolean
	*/
	public function checkUsernameExists($username, $id = 0) {
		$user = $this->getByUsername($username);
		return $user && $user->id != intval($id);
	}

	/*
 		Function: checkEmailExists
 			Method to check if a email already exists.

		Parameters:
			$email - User email address
			$id - User identifier

		Returns:
			Boolean
	*/
	public function checkEmailExists($email, $id = 0) {
		$user = $this->getByEmail($email);
		return $user && $user->id != intval($id);
	}

	/*
 		Function: isJoomlaAdmin
 			Method to check if a user is a Joomla Admin

		Parameters:
			$user - JUser User

		Returns:
			Boolean
	*/
    public function isJoomlaAdmin(JUser $user) {
		if ($this->app->joomla->isVersion('1.5')) {
			return in_array(strtolower($user->usertype), array('superadministrator', 'super administrator', 'administrator')) || $user->gid == 25 || $user->gid == 24;
		} else {
			return $user->authorise('core.login.admin');
		}
    }

	/*
 		Function: isJoomlaSuperAdmin
 			Method to check if a user is a Joomla Super Admin

		Parameters:
			$user - JUser User

		Returns:
			Boolean
	*/
    public function isJoomlaSuperAdmin(JUser $user) {
		if ($this->app->joomla->isVersion('1.5')) {
			return in_array(strtolower($user->usertype), array('superadministrator', 'super administrator')) || $user->gid == 25;
		} else {
			return $user->authorise('core.admin');
		}
    }

	/*
 		Function: getBrowserDefaultLanguage
 			Returns the users browser default language

		Returns:
			String - the users browser default language
	*/
	public function getBrowserDefaultLanguage() {
		$langs = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

			if (count($lang_parse[1])) {

				$langs = array_combine($lang_parse[1], $lang_parse[4]);

				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}

				arsort($langs, SORT_NUMERIC);
			}
		}

		return array_shift(explode('-', array_shift(array_keys($langs))));

	}

	/*
 		Function: canAccess
 			Method to check if a user can access a resource

		Parameters:
			$user - JUser User
			$access - the access level to check against

		Returns:
			Boolean
	*/
	public function canAccess($user = null, $access = 0) {

		if (is_null($user)) {
			$user = $this->get();
		}

		if ($this->app->joomla->isVersion('1.5')) {
			return (int) $user->get('aid', 0) >= $access;
		} else {
			return in_array($access, $user->getAuthorisedViewLevels());
		}

	}

	/*
 		Function: getDBAccessString
 			Wrapper method to get the users db access string

		Parameters:
			$user - JUser User

		Returns:
			Boolean
	*/
	public function getDBAccessString($user = null) {

		if (is_null($user)) {
			$user = $this->get();
		}

		if ($this->app->joomla->isVersion('1.5')) {
			return "access <= ".(int) $user->get('aid', 0);
		} else {
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			return "access IN ($groups)";
		}
	}

}