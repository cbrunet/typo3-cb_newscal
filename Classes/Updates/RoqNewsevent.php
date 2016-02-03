<?php

namespace Cbrunet\CbNewscal\Updates;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
class RoqNewsevent extends AbstractUpdate {


	protected $title = 'EXT:cb_newscal Migrate roq_newsevent to eventnews';

	/**
	 * Checks whether updates are required.
	 *
	 * @param string &$description The description for the update
	 * @return bool Whether an update is required (TRUE) or not (FALSE)
	 */
	public function checkForUpdate(&$description) {
		$status = FALSE;

		$firstContentElementRow = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'tx_news_domain_model_news', '1=1');
		if (isset($firstContentElementRow['tx_roqnewsevent_is_event'])) {
			if (isset($firstContentElementRow['is_event'])) {
				$countRows = $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_news_domain_model_news', 'tx_roqnewsevent_is_event=1');
				if ($countRows === 0) {
					$description = 'No event need to be migrated.';
				}
				else {
					$description = sprintf('The database contains <strong>%s</strong> events from roq_newsevent which will be migrated!', $countRows);
					$status = TRUE;
				}
			}
			else {
				$description = "eventnews extension not installed. Cannot migrate events!";
			}
		} else {
			$description = 'No event need to be migrated.';
		}

		return $status;
	}

	/**
	 * Performs the accordant updates.
	 *
	 * @param array &$dbQueries Queries done in this update
	 * @param mixed &$customMessages Custom messages
	 * @return bool Whether everything went smoothly or not
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {
		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows('*', 'tx_news_domain_model_news', 'tx_roqnewsevent_is_event=1');
		foreach ($rows as $row) {
			$start = $row['tx_roqnewsevent_startdate'] + $row['tx_roqnewsevent_starttime'];
			$end = $row['tx_roqnewsevent_enddate'] + $row['tx_roqnewsevent_endtime'];
			if ($end > 0 && $row['tx_roqnewsevent_enddate'] == 0)
			{
				$end += $row['tx_roqnewsevent_startdate'];  // if end time and no end date, use start date and end time
			}
			$update = array(
				'is_event' => '1',
				'tx_roqnewsevent_is_event' => '0',
				'full_day' => $row['tx_roqnewsevent_starttime'] == 0 ? 1 : 0,
				'datetime' => $start,
				'event_end' => $end,
				'archive' => $end ? $end : $start, // archive on end date
				'location_simple' => $row['tx_roqnewsevent_location']
			);
			$this->getDatabaseConnection()->exec_UPDATEquery(
				'tx_news_domain_model_news',
				'uid=' . $row['uid'],
				$update
			);
		}

		return TRUE;
	}


	/**
	 * @return DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}