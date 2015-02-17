<?php

namespace Cbrunet\CbNewscal\Controller;

class NewscalController extends \Tx_News_Controller_NewsController {

	/**
	 * @var Tx_News_Domain_Repository_NewsRepository
	 */
	protected $newsRepository;

	protected $year;
	protected $month;


	/**
	 * 
	 *
	 * @param array $overwriteDemand
	 * @return void
	 */
	public function calendarAction(array $overwriteDemand = NULL) {
		$demand = $this->createDemandObject($overwriteDemand);
		$monthsBefore = (int)$this->settings['monthsBefore'];
		$monthsAfter = (int)$this->settings['monthsAfter'];

		$calendars = array();

		for ($month = $demand->getMonth() - $monthsBefore; $month <= $demand->getMonth() + $monthsAfter; $month++) {
			$cm = mktime(0, 0, 0, $month, 1, $demand->getYear());
			$curdemand = clone $demand;
			$curdemand->setYear((int)date('Y', $cm));
			$curdemand->setMonth((int)date('n', $cm));
			$newsRecords = $this->newsRepository->findDemanded($curdemand);
			$calendars[] = array('news' => $newsRecords, 'year' => $curdemand->getYear(), 'month' => $curdemand->getMonth(), 'curmonth' => $month == $demand->getMonth()?1:0);
		}

		$this->view->assignMultiple(array(
			'calendars' => $calendars,
			'navigation' => $this->createNavigationArray()
		));
	}


	protected function createDemandObject($overwriteDemand) {
		if ($this->settings['dateField'] == 'eventStartdate') {
			$this->newsRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_RoqNewsevent_Domain_Repository_EventRepository');
		}

		$demand = parent::createDemandObjectFromSettings($this->settings);

		if ($overwriteDemand !== NULL) {
			$demand = $this->overwriteDemandObject($demand, $overwriteDemand);
		}

		if ($demand->getYear() == NULL) {
			$demand->setYear((int)date('Y'));
		}
		if ($demand->getMonth() == NULL) {
			$demand->setMonth((int)date('n'));
		}
		$this->year = $demand->getYear();
		$this->month = $demand->getMonth();

		$demand->setOrder($demand->getDateField() . ' asc');
		$demand->setTopNewsFirst(0);

		if ($overwriteDemand === NULL) {
			$this->adjustDemand($demand);  // Use settings.displayMonth only if no demand object
		}

		return $demand;
	}


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
		$navigation['prev']['month'] = date('m', $prevdate);
		$navigation['prev']['year'] = date('Y', $prevdate);
		$nextdate = mktime(0, 0, 0, $this->month + $monthsToScroll, 1, $this->year);
		$navigation['next']['month'] = date('m', $nextdate);
		$navigation['next']['year'] = date('Y', $nextdate);

		$this->contentObj = $this->configurationManager->getContentObject();
		$navigation['uid'] = $this->contentObj->data['uid'];

		return $navigation;
	}

	protected function adjustDemand(&$demand) {
		$displayMonth = $this->settings['displayMonth'];

		// Display relative to current month
		if (strlen($displayMonth) > 1 && ($displayMonth[0] == '-' || $displayMonth[0] == '+')) {
			$displayMonth = (int)$displayMonth;
			$mt = mktime(0, 0, 0, $demand->getMonth() + $displayMonth, 1, $demand->getYear());
			$demand->setYear((int)date('Y', $mt));
			$demand->setMonth((int)date('n', $mt));
			return;
		}

		// Display absolute month
		if (strlen($displayMonth) == 7 && $displayMonth[4] == '-') {
			$demand->setYear((int)substr($displayMonth, 0, 4));
			$demand->setMonth((int)substr($displayMonth, 5, 2));
			return;
		}
	}

}
