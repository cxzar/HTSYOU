<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementEvent
		Element events.
*/
class ElementEvent {

	public static function beforeDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// element will not be rendered if $event['render'] is set to false
		$event['render'] = true;

	}

	public static function afterDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

	public static function beforeSubmissionDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// element will not be rendered if $event['render'] is set to false
		$event['render'] = true;

	}

	public static function afterSubmissionDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

	public static function configParams($event) {

		$element = $event->getSubject();

		// set events ReturnValue after modifying $params
		$params = $event->getReturnValue();
		$event->setReturnValue($params);

	}

	public static function configForm($event) {

		$element = $event->getSubject();
		$form = $event['form'];

	}

	public static function configXML($event) {

		$element = $event->getSubject();
		$xml = $event['xml'];

	}

	public static function download($event) {

		$download_element = $event->getSubject();
		$check = $event['check'];
	}

	public static function afterEdit($event) {

		$element = $event->getSubject();

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

}