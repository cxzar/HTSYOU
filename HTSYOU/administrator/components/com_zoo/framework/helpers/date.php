<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DateHelper
		Date helper class.
*/
class DateHelper extends AppHelper {

	public function create($time = 'now', $offset = 0) {
		return $this->_call(array('JFactory', 'getDate'), array($time, $offset));
	}

	public function isToday($date) {

		// get dates
		$now  = $this->create();
		$date = $this->create($date);

		return date('Y-m-d', $date->toUnix(true)) == date('Y-m-d', $now->toUnix(true));
	}

	public function isYesterday($date) {

		// get dates
		$now  = $this->create();
		$date = $this->create($date);

		return date('Y-m-d', $date->toUnix(true)) == date('Y-m-d', $now->toUnix(true) - 86400);
	}

	public function getDeltaOrWeekdayText($date) {

		// get dates
		$now   = $this->create();
		$date  = $this->create($date);
		$delta = $now->toUnix(true) - $date->toUnix(true);

		if ($this->isToday($date->toMySQL())) {
			$hours = intval($delta / 3600);
			$hours = $hours > 0 ? $hours.JText::_('hr') : '';
			$mins  = intval(($delta % 3600) / 60);
			$mins  = $mins > 0 ? ' '.$mins.JText::_('min') : '';
			$delta = $hours.$mins ? JText::sprintf('%s ago', $hours.$mins) : JText::_('1min ago');
		} else {
			$delta = JHTML::_('date', $date->toMySQL(true), JText::_('DATE_FORMAT_LC3').' %H:%M');
		}

		return $delta;
	}

	public function format($format) {
		return $this->app->joomla->isVersion('1.5') ? $format : $this->strftimeToDateFormat($format);
	}

	public function dateFormatToStrftime($dateFormat) {
		return strtr((string) $dateFormat, $this->_getDateFormatToStrftimeMapping());
	}

	public function strftimeToDateFormat($strftime) {
		return strtr((string) preg_replace("/(?<![\%|\\\\])(\w)/i", '\\\\$1', $strftime), array_flip($this->_getDateFormatToStrftimeMapping()));
	}

	protected function _getDateFormatToStrftimeMapping() {
		return array(
			// Day - no strf eq : S
			'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
			// Week - no date eq : %U, %W
			'W' => '%V',
			// Month - no strf eq : n, t
			'F' => '%B', 'm' => '%m', 'M' => '%b',
			// Year - no strf eq : L; no date eq : %C, %g
			'o' => '%G', 'Y' => '%Y', 'y' => '%y',
			// Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
			'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
			// Timezone - no strf eq : e, I, P, Z
			'O' => '%z', 'T' => '%Z',
			// Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
			'U' => '%s'
		);
	}

	public function getOffset($user = null) {

		$user = $user == null ? $this->app->user->get() : $user;
		return $user->getParam('timezone', $this->app->system->config->getValue('config.offset'));

	}
}