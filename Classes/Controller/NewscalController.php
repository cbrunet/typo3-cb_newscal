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


		$this->contentObj = $this->configurationManager->getContentObject();
		$uid = $this->contentObj->data['uid'];

		$newsRecords = $this->newsRepository->findDemanded($demand);

		$this->view->assignMultiple(array(
			'news' => $newsRecords,
			'overwriteDemand' => $overwriteDemand,
			'demand' => $demand,
			'uid' => $uid,
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
