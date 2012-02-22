<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TwitterHelper
		Twitter helper class.
*/
class TwitterHelper extends AppHelper {

	/*
		Function: client
			Get Twitter Client.

		Returns:
			Twitter on success, else null
	*/
	public function client() {

		// get comment params
		$application = $this->app->zoo->getApplication();
		$params = $this->app->parameter->create()->loadArray($application ? $application->getParams()->get('global.comments.') : array());

		if (!function_exists('curl_init')) {
			return null;
		}

		// load twitter classes
		$this->app->loader->register('TwitterOAuth', 'libraries:twitter/twitteroauth.php');

		$oauth_token = null;
		$oauth_token_secret = null;
		if (isset($_SESSION['twitter_oauth_token']) && isset($_SESSION['twitter_oauth_token_secret'])) {
			$oauth_token = $_SESSION['twitter_oauth_token'];
			$oauth_token_secret = $_SESSION['twitter_oauth_token_secret'];
		}

		// Build TwitterOAuth object with client credentials.
		return new TwitterOAuth($params->get('twitter_consumer_key'), $params->get('twitter_consumer_secret'), $oauth_token, $oauth_token_secret);

	}

	/*
		Function: fields
			Get Twitter Fields.

		Parameters:
			$t_uid - Twitter user id
			$fields - Fields to acquire

		Returns:
			Array - fields
	*/
	public function fields($t_uid, $fields = null) {
		try {

			$connection = $this->client();
			if ($connection) {
				$infos = $connection->get('users/show.json?user_id='.$t_uid);

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
			Logout from Twitter.

		Returns:
			TwitterHelper
	*/
	public function logout() {
		// remove access token from session
		$_SESSION['twitter_oauth_token'] = null;
		$_SESSION['twitter_oauth_token_secret'] = null;
	}

}
