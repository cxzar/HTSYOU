<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CategoryEvent
		Category events.
*/
class CategoryEvent {

	public static function init($event) {

		$category = $event->getSubject();

	}

	public static function saved($event) {

		$category = $event->getSubject();
		$new = $event['new'];

	}

	public static function deleted($event) {

		$category = $event->getSubject();

	}

	public static function stateChanged($event) {

		$category = $event->getSubject();
		$old_state = $event['old_state'];

	}

}
