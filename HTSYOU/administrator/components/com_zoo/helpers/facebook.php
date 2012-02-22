<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: FacebookHelper
		Facebook helper class.
*/
class FacebookHelper extends AppHelper {

	/*
		Function: client
			Get Facebook Client.

		Returns:
			Facebook on success, else null
	*/
	public function client() {

		// get comment params
		$application = $this->app->zoo->getApplication();
		$params = $this->app->parameter->create()->loadArray($application ? $application->getParams()->get('global.comments.') : array());

		if (!function_exists('curl_init')) {
			return null;
		}

		// load facebook classes
		$this->app->loader->register('Facebook', 'libraries:facebook/facebook.php');

		$access_token = null;
		if (isset($_SESSION['facebook_access_token'])) {
			$access_token = $_SESSION['facebook_access_token'];
		}

		// Build FacebookOAuth object with client credentials.
		return new Facebook($this->app, array('app_id' => $params->get('facebook_app_id'), 'app_secret' => $params->get('facebook_app_secret'), 'access_token' => $access_token));

	}

	/*
		Function: fields
			Get Facebook Fields.

		Parameters:
			$fb_uid - Facebook user id
			$fields - Fields to acquire

		Returns:
			Array - fields
	*/
	public function fields($fb_uid, $fields = null) {
		try {

			$connection = $this->client();
			if ($connection) {

				$infos = $connection->getProfile($fb_uid);

				if (is_object($infos)) {
					if (is_array($fields)) {
						return array_intersect_key((array)$infos, array_flip($fields));
					} else {
						return (array)$infos;
					}
				}
			}

		} catch (Exception $e) {}
	}

	/*
		Function: logout
			Logout from Facebook.

		Returns:
			FacebookHelper
	*/
	public function logout() {
		// remove access token from session
		$_SESSION['facebook_access_token'] = null;
		return $this;
	}

}
