<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: UpdateHelper
		The general helper Class for updates
*/
class UpdateHelper extends AppHelper {

    /*
		Function: requiresUpdate
			Checks if ZOO needs to be updated.

		Returns:
			bool - true if ZOO needs to be updated
	*/
	public function required() {
		$updates = $this->getRequiredUpdates();
		return !empty($updates);
	}

    /*
		Function: getRequiredUpdates
			Return required update versions.

		Returns:
			Array - versions of required updates
	*/
	public function getRequiredUpdates() {

		// get current version
		$current_version = $this->app->zoo->version();

		// find required updates
		if ($files = $this->app->path->files('updates:', false, '/^\d+.*\.php$/')) {
			$files = array_map(create_function('$file', 'return basename($file, ".php");'), array_filter($files, create_function('$file', 'return version_compare("'.$current_version.'", basename($file, ".php")) < 0;')));
			usort($files, create_function('$a, $b', 'return version_compare($a, $b);'));
		}

		return $files;
	}

    /*
		Function: getNotifications
			Get preupdate notifications.

		Returns:
			Array - messages
	*/
	public function getNotifications() {

		// check if update is required
		if (!$this->required()) {
			return $this->_createResponse('No update required.', false, false);
		}

		// get current version
		$current_version = $this->app->zoo->version();

		$notifications = array();

		// find and run the next update
		foreach ($this->getRequiredUpdates() as $version) {
			if ((version_compare($version, $current_version) > 0)) {
				$class = 'Update'.str_replace('.', '', $version);
				$this->app->loader->register($class, "updates:$version.php");

				if (class_exists($class)) {

					// make sure class implemnts iUpdate interface
					$r = new ReflectionClass($class);
					if ($r->isSubclassOf('iUpdate') && !$r->isAbstract()) {

						// run the update
						$notification = $r->newInstance()->getNotifications($this->app);
						if (is_array($notification)) {
							$notifications = array_merge($notifications, $notification);
						}

					}
				}
			}
		}

		return $notifications;

	}

    /*
		Function: run
			Performs the next update.

		Returns:
			Array - response array
	*/
	public function run() {

		// check if update is required
		if (!$this->required()) {
			return $this->_createResponse('No update required.', false, false);
		}

		// get current version
		$current_version = $this->app->zoo->version();

		// find and run the next update
		$updates = $this->getRequiredUpdates();
		foreach ($updates as $version) {
			if ((version_compare($version, $current_version) > 0)) {
				$class = 'Update'.str_replace('.', '', $version);
				$this->app->loader->register($class, "updates:$version.php");

				if (class_exists($class)) {

					// make sure class implemnts iUpdate interface
					$r = new ReflectionClass($class);
					if ($r->isSubclassOf('iUpdate') && !$r->isAbstract()) {

						try {

							// run the update
							$r->newInstance()->run($this->app);

						} catch (Exception $e) {

							return $this->_createResponse("Error during update! ($e)", true, false);

						}

						// set current version
						$version_string = $version;
						if (!$required = count($updates) > 1) {
							if (($xml = simplexml_load_file($this->app->path->path('component.admin:zoo.xml'))) && (string) $xml->name == 'ZOO') {
								$version_string = (string) $xml->version;
							}
						}
						$this->setVersion($version);
						return $this->_createResponse('Successfully updated to version '.$version_string, false, $required);
					}
				}
			}
		}

		return $this->_createResponse('No update found.', false, false);

	}

    /*
		Function: refreshDBTableIndexes
			Drops and recreates all ZOO database table indexes.

		Returns:
			void
	*/
	public function refreshDBTableIndexes() {

		// sanatize table indexes
		if ($this->app->path->path('component.admin:installation/index.sql')) {

			$db = $this->app->database;

			// read index.sql
			$buffer = JFile::read($this->app->path->path('component.admin:installation/index.sql'));

			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (!empty($queries)) {

				foreach($queries as $query) {

					// replace table prefixes
					$query = $db->replacePrefix($query);

					// parse table name
					preg_match('/ALTER\s*TABLE\s*`(.*)`/i', $query, $result);

					if (count($result) < 2) {
						continue;
					}

					$table = $result[1];

					// check if table exists
					if (!$db->queryResult('SHOW TABLES LIKE ' . $db->Quote($table))) {
						continue;
					}

					// get existing indexes
					$indexes = $db->queryObjectList('SHOW INDEX FROM ' . $table);

					// drop existing indexes
					$removed = array();
					foreach ($indexes as $index) {
						if (in_array($index->Key_name, $removed)) {
							continue;
						}
						if ($index->Key_name != 'PRIMARY') {
							$db->query('DROP INDEX ' . $index->Key_name . ' ON ' . $table);
							$removed[] = $index->Key_name;
						}
					}

					// add new indexes
					$db->query($query);
				}
			}
		}
	}

    /*
		Function: setVersion
			Writes the current version in versions table.

		Returns:
			void
	*/
	public function setVersion($version) {

		// remove previous versions
		$this->app->database->query('TRUNCATE TABLE ' . ZOO_TABLE_VERSION);

		// set version
		$this->app->database->query('INSERT INTO '.ZOO_TABLE_VERSION.' SET version=' . $this->app->database->Quote($version));
	}

	protected function _createResponse($message, $error, $continue) {
		$message = JText::_($message);
		return compact ('message', 'error', 'continue');
	}

    /*
		Function: available
			Checks if there is a new update available.

		Returns:
			 Misc - String if update available, false otherwise
	*/
	public function available() {

		// check for updates
		if($xml = simplexml_load_file($this->app->path->path('component.admin:zoo.xml'))){

			// update check
			if ($url = current($xml->xpath('//updateUrl'))) {

				// create check url
				$url = sprintf('%s?application=%s&version=%s&format=raw', $url, $this->app->joomla->isVersion('1.5') ? 'zoo_j15' : 'zoo_j17', urlencode(current($xml->xpath('//version'))));

				// only check once a day
				$hash    = md5($url.date('Y-m-d'));
				$zoo_data = file_exists($this->app->path->path("cache:zoo_update_cache"))
							? unserialize(file_get_contents($this->app->path->path("cache:zoo_update_cache")))
							: array("data"=>'{}',"check"=>'');


				if ($zoo_data["check"] != $hash) {
					if ($request = $this->app->http->get($url)) {
						$zoo_data["check"] = $hash;
						$zoo_data["data"] = $request['body'];
					}
				}

				// decode response and set message
				if (!$this->app->system->session->get('com_zoo.hideUpdateNotification') && ($data = json_decode($zoo_data["data"])) && $data->status == 'update-available') {
					$close = '<span onclick="jQuery.ajax(\''.$this->app->link(array('controller' => 'manager', 'task' => 'hideUpdateNotification')).'\'); jQuery(this).closest(\'ul\').hide();" class="hide-update-notification"></span>';
					$this->app->system->application->enqueueMessage($data->message.$close, 'notice');
				}

				@file_put_contents($this->app->path->path("cache:").'/zoo_update_cache', serialize($zoo_data));
			}

		}
	}

	public function hideUpdateNotification() {
		$this->app->system->session->set('com_zoo.hideUpdateNotification', true);
	}

}

interface iUpdate {

    /*
		Function: getNotifications
			Get preupdate notifications.

		Returns:
			Array - messages
	*/
	public function getNotifications($app);

    /*
		Function: run
			Performs the update.

		Returns:
			bool - true if updated successful
	*/
	public function run($app);

}

/*
	Class: UpdateAppException
*/
class UpdateAppException extends AppException {}