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
class PluginUpdate extends AbstractUpdate {


	protected $title = 'EXT:cb_newscal Update plugins';

	/**
	 * Checks whether updates are required.
	 *
	 * @param string &$description The description for the update
	 * @return bool Whether an update is required (TRUE) or not (FALSE)
	 */
	public function checkForUpdate(&$description) {
		$status = FALSE;

		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows('*', 'tt_content', 'CType="list" AND list_type="news_pi1"');
		$count = 0;
		foreach ($rows as $row) {
			if (FALSE !== strpos($row['pi_flexform'], "Newscal-&gt;calendar"))
			{
				$count += 1;
			}
		}

		if ($count > 0) {
			$description = sprintf('The database contains <strong>%s</strong> plugins to convert!', $count);
			$status = TRUE;
		} else {
			$description = 'No plugin needs to be migrated.';
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
		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows('*', 'tt_content', 'CType="list" AND list_type="news_pi1"');
		foreach ($rows as $row) {
			if (FALSE !== strpos($row['pi_flexform'], "Newscal-&gt;calendar"))
			{
				$ff = str_replace('Newscal-&gt;calendar', 'News-&gt;calendar', $row['pi_flexform']);
				$ff = str_replace('eventStartdate', 'dateTime', $ff);

				$update = array(
					'pi_flexform' => $ff
				);
				$this->getDatabaseConnection()->exec_UPDATEquery(
					'tt_content',
					'uid=' . $row['uid'],
					$update
				);
			}
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