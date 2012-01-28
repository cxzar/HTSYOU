<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: TagEvent
		Tag events.
*/
class TagEvent {

	public static function saved($event) {

		$tags = (array) $event->getSubject();
		$item = $event['item'];

	}

	public static function deleted($event) {

		$tags = (array) $event->getSubject();
		$application = $event['application'];
		
	}

}
