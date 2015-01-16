<?php

namespace Cbrunet\CbNewscal\Controller;

class NewscalController extends \Tx_News_Controller_NewsController {

	/**
	 * @var Tx_News_Domain_Repository_NewsRepository
	 */
	protected $newsRepository;

	/**
	 * 
	 *
	 * @param array $overwriteDemand
	 * @return void
	 */
	public function calendarAction(array $overwriteDemand = NULL) {
		$demand = $this->createDemandObjectFromSettings($this->settings);

		if ($overwriteDemand !== NULL) {
			$demand = $this->overwriteDemandObject($demand, $overwriteDemand);
		}

		if ($demand->getYear() == NULL) {
			$demand->setYear((int)date('Y'));
		}
		if ($demand->getMonth() == NULL) {
			$demand->setMonth((int)date('n'));
		}
		$demand->setOrder($demand->getDateField() . ' asc');
		$demand->setTopNewsFirst(0);

		if ($overwriteDemand === NULL) {
			$this->adjustDemand($demand);  // Use settings.displayMonth only if no demand object
		}

		$monthsBefore = (int)$this->settings['monthsBefore'];
		$monthsAfter = (int)$this->settings['monthsAfter'];
		$navigation = array();
		switch ((int)$this->settings['scrollMode']) {
			case -1:
				$navigation['monthsToScroll'] = $monthsBefore + $monthsAfter > 0 ? $monthsBefore + $monthsAfter : 1;
				break;
			case 0:
				$navigation['monthsToScroll'] = $monthsBefore + 1 + $monthsAfter;
				break;
			default:
				$navigation['monthsToScroll'] = (int)$this->settings['scrollMode'];
				break;
		}
		$navigation['numberOfMonths'] = $monthsBefore + 1 + $monthsAfter;
		$calendars = array();

		for ($month = $demand->getMonth() - $monthsBefore; $month <= $demand->getMonth() + $monthsAfter; $month++) {
			$cm = mktime(0, 0, 0, $month, 1, $demand->getYear());
			$curdemand = clone $demand;
			$curdemand->setYear((int)date('Y', $cm));
			$curdemand->setMonth((int)date('n', $cm));
			$newsRecords = $this->newsRepository->findDemanded($curdemand);
			$calendars[] = array('news' => $newsRecords, 'demand' => $curdemand, 'curmonth' => $month == $demand->getMonth()?1:0);
		}

		$this->contentObj = $this->configurationManager->getContentObject();
		$navigation['uid'] = $this->contentObj->data['uid'];

		$this->view->assignMultiple(array(
			'calendars' => $calendars,
			'navigation' => $navigation,
			'demand' => $demand
		));
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
