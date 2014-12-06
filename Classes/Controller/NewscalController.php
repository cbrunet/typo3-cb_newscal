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

}
