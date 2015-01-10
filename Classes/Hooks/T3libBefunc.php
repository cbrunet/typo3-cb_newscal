<?php

namespace Cbrunet\CbNewscal\Hooks;

class T3libBefunc extends \Tx_News_Hooks_T3libBefunc {

	protected $currentMonth = <<<EOT
					<settings.displayMonth>
						<TCEforms>
						<label>LLL:EXT:cb_newscal/Resources/Private/Language/locallang_be.xlf:flexforms_general.displayMonth</label>
							<config>
								<default></default>
								<type>input</type>
								<size>15</size>
							</config>
						</TCEforms>
					</settings.displayMonth>
EOT;

	/**
	 * Remove unused fields in the flexform.
	 */
	public function updateFlexforms(&$params, &$reference) {
		if ($params['selectedView'] == 'Newscal->calendar') {
			$removedFields = array(
				'sDEF' => 'orderBy,orderDirection,timeRestriction,timeRestrictionHigh,singleNews',
				'additional' => 'limit,offset,topNewsFirst,hidePagination,excludeAlreadyDisplayedNews,disableOverrideDemand',
				'template' => '',
			);
			$this->deleteFromStructure($params['dataStructure'], $removedFields);
			unset($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'][0]);  // Remove empty field
			$params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'][] = array('LLL:EXT:news/Resources/Private/Language/locallang_be.xml:flexforms_general.orderBy.tstamp', 'tstamp');
			$params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'][] = array('LLL:EXT:news/Resources/Private/Language/locallang_be.xml:flexforms_general.orderBy.crdate', 'crdate');

			$displayMonth = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->currentMonth);


			$params['dataStructure']['sheets']['sDEF']['ROOT']['el'] = array_slice($params['dataStructure']['sheets']['sDEF']['ROOT']['el'], 0, 1, true) +
			                                                           array('settings.displayMonth' => $displayMonth) +
															           array_slice($params['dataStructure']['sheets']['sDEF']['ROOT']['el'], 1, NULL, true);
		}
	}
}
