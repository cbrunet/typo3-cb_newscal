<?php

namespace Cbrunet\CbNewscal\Controller;

class NewsController extends \GeorgRinger\News\Controller\NewsController {


	protected $year;
	protected $month;

	protected $events;


	/**
	 * 
	 *
	 * @param array $overwriteDemand
	 * @return void
	 */
	public function calendarAction(array $overwriteDemand = NULL) {
		$months = array();
		$demand = $this->createDemandObject($overwriteDemand);
		$monthsBefore = (int)$this->settings['monthsBefore'];
		$monthsAfter = (int)$this->settings['monthsAfter'];

		for ($m = $this->month - $monthsBefore; $m <= $this->month + $monthsAfter; $m++) {
			$cm = mktime(0, 0, 0, $m, 1, $this->year);
			$month = date('n', $cm);
			$year = date('Y', $cm);
			$months[] = array(
				'month' => $month,
				'year' => $year,
				'curmonth' => ($month == $this->month),
				'weeks' => $this->getWeeks($demand, $month, $year)
			);
		}

		$this->view->assignMultiple(array(
			'months' => $months,
			'navigation' => $this->createNavigationArray()
		));
	}

	protected function createDemandObject($overwriteDemand) {
		$this->year = (int)date('Y');
		$this->month = (int)date('n');


        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews')) {
			$demand = $this->createDemandObjectFromSettings($this->settings, 'GeorgRinger\\Eventnews\\Domain\\Model\\Dto\\Demand');
		}
		else {
			$demand = $this->createDemandObjectFromSettings($this->settings);
		}
		if ($overwriteDemand !== NULL) {
			$demand = $this->overwriteDemandObject($demand, $overwriteDemand);
		}

		if ($demand->getYear() !== NULL) {
			$this->year = $demand->getYear();
			$demand->setYear(NULL);
		}
		if ($demand->getMonth() !== NULL) {
			$this->month = $demand->getMonth();
			$demand->setMonth(NULL);
		}

		$demand->setOrder($demand->getDateField() . ' asc');

		if ($overwriteDemand === NULL) {
			// Use settings.displayMonth only if no demand object
			$displayMonth = $this->settings['displayMonth'];

			// Display relative to current month
			if (strlen($displayMonth) > 1 && ($displayMonth[0] == '-' || $displayMonth[0] == '+')) {
				$displayMonth = (int)$displayMonth;
				$mt = mktime(0, 0, 0, $this->month + $displayMonth, 1, $this->year);
				$this->year = (int)date('Y', $mt);
				$this->month = (int)date('n', $mt);
			}

			// Display absolute month
			if (strlen($displayMonth) == 7 && $displayMonth[4] == '-') {
				$this->year = (int)substr($displayMonth, 0, 4);
				$this->month = (int)substr($displayMonth, 5, 2);
			}
		}

		return $demand;
	}

	protected function getEventsOfDay($demand, &$day)
	{
		if (!isset($this->events[$day['year']]))
			$this->events[$day['year']] = array();
		if (!isset($this->events[$day['year']][$day['month']]))
		{
			$demand->setYear($day['year']);
			$demand->setMonth($day['month']);
			$this->events[$day['year']][$day['month']] = $this->newsRepository->findDemanded($demand);
		}

		$day['startev'] = True;
		$day['endev'] = True;
		$day['news'] = array();
		foreach ($this->events[$day['year']][$day['month']] as $k => $event)
		{
			switch ($demand->getDateField())
			{
				case 'datetime':
        			if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews'))
        			{
        				if ($event->getEventEnd())
        				{
        					if ($event->getDatetime()->format('Y-m-d') <= $day['cd'] &&
        						$event->getEventEnd()->format('Y-m-d') >= $day['cd'] )
        					{
        						$day['news'][] = $event;
        						if ($event->getDatetime()->format('Y-m-d') < $day['cd'])
        						{
        							$day['startev'] = False;
        						}
        						if ($event->getEventEnd()->format('Y-m-d') > $day['cd'])
        						{
        							$day['endev'] = False;
        						}
        						else
        						{
        						}
        					}
        					continue 2;  // next loop iteration
        				}
        			}


					if ($event->getDatetime()->format('Y-m-d') == $day['cd'])
					{
						$day['news'][] = $event;
					}
					break;

				case 'archive':
					if ($event->getArchive()->format('Y-m-d') == $day['cd'])
					{
						$day['news'][] = $event;
					}
					break;

				case 'crdate':
					if ($event->getCrdate()->format('Y-m-d') == $day['cd'])
					{
						$day['news'][] = $event;
					}
					break;

				case 'tstamp':
					if ($event->getTstamp()->format('Y-m-d') == $day['cd'])
					{
						$day['news'][] = $event;
					}
					break;

			}
		}
		
	}


	protected function getWeeks($demand, $month, $year) {
		$curday = $this->firstDayOfMonth($month, $year);
		$lastday = date('t', mktime(0, 0, 0, $month, 1, $year));
		$weeks = array();
		while ($curday <= $lastday) {
			$week = array();
			for ($d=0; $d<7; $d++) {
				$day = array();
				$dts = mktime(0, 0, 0, $month, $curday, $year);
				$day['ts'] = $dts;
				$day['day'] = date('j', $dts);
				$day['month'] = date('n', $dts);
				$day['year'] = date('Y', $dts);
				$day['cd'] = date('Y-m-d', $dts);
				$day['curmonth'] = $day['month'] == $month;
				$day['curday'] = date('Ymd') == date('Ymd', $day['ts']);

				$this->getEventsOfDay($demand, $day);

				$week[] = $day;
				$curday++;
			}
			$weeks[] = $week;
		}

		$demand->setYear(NULL);
		$demand->setMonth(NULL);
		$demand->setDay(NULL);
		return $weeks;
	}

	/**
	 * Return the first day of the month as (nagative) offset from day 1
	 *
	 * @return int
	 **/
	protected function firstDayOfMonth($month, $year) {
		$fdom = mktime(0, 0, 0, $month, 1, $year);  // First day of the month
		$fdow = (int)date('w', $fdom);  // Day of week of the first day

		$fd = 1 - $fdow + $this->settings['firstDayOfWeek'];  // First day of the calendar
		if ($fd > 1) {
			$fd -= 7;
		}
		return $fd;
	}


	/**
	 * Create the array needed for navigation.
	 *
	 * Returned array contains:
	 *    uid:            uid of current content object
	 *    numberOfMonths: number of displayed months
	 *    prev:           month and year for previous arrow
	 *    next:           month and year for mext arrow
	 *
	 * @return array
	 **/
	protected function createNavigationArray() {
		$navigation = array('prev' => array(), 'next' => array());
		$monthsBefore = (int)$this->settings['monthsBefore'];
		$monthsAfter = (int)$this->settings['monthsAfter'];
		$navigation['numberOfMonths'] = $monthsBefore + 1 + $monthsAfter;
		
		switch ((int)$this->settings['scrollMode']) {
			case -1:
				$monthsToScroll = $monthsBefore + $monthsAfter > 0 ? $monthsBefore + $monthsAfter : 1;
				break;
			case 0:
				$monthsToScroll = $monthsBefore + 1 + $monthsAfter;
				break;
			default:
					$monthsToScroll = (int)$this->settings['scrollMode'];
				break;
		}

		$prevdate = mktime(0, 0, 0, $this->month - $monthsToScroll, 1, $this->year);
		if ($this->settings['timeRestriction']) {
			$ts = strtotime($this->settings['timeRestriction']);
			$trm = mktime(0, 0, 0, date('n', $ts), 1, date('Y', $ts));
			if ($prevdate < $trm) {
				$navigation['prev'] = NULL;
			}
		}
		if (is_array($navigation['prev'])) {
			$navigation['prev']['month'] = date('m', $prevdate);
			$navigation['prev']['year'] = date('Y', $prevdate);
		}

		$nextdate = mktime(0, 0, 0, $this->month + $monthsToScroll, 1, $this->year);
		if ($this->settings['timeRestrictionHigh']) {
			$ts = strtotime($this->settings['timeRestrictionHigh']);
			$trm = mktime(0, 0, 0, date('n', $ts), 1, date('Y', $ts));
			if ($nextdate > $trm) {
				$navigation['next'] = NULL;
			}
		}
		if (is_array($navigation['next'])) {
			$navigation['next']['month'] = date('m', $nextdate);
			$navigation['next']['year'] = date('Y', $nextdate);
		}

		$this->contentObj = $this->configurationManager->getContentObject();
		$navigation['uid'] = $this->contentObj->data['uid'];

		return $navigation;
	}

}
