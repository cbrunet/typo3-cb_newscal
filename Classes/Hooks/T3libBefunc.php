<?php

/**
 * Remove unused fields in the flexform.
 */
function updateFlexforms(&$params, &$reference) {
	if ($params['selectedView'] == 'Newscal->calendar') {
		$removedFields = array(
			'sDEF' => 'orderBy,orderDirection,timeRestriction,timeRestrictionHigh,singleNews',
			'additional' => 'limit,offset,topNewsFirst,hidePagination,excludeAlreadyDisplayedNews,disableOverrideDemand',
			'template' => '',
		);
		deleteFromStructure($params['dataStructure'], $removedFields);
	}
}

function deleteFromStructure(array &$dataStructure, array $fieldsToBeRemoved) {
	foreach ($fieldsToBeRemoved as $sheetName => $sheetFields) {
		$fieldsInSheet = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $sheetFields, TRUE);

		foreach ($fieldsInSheet as $fieldName) {
			unset($dataStructure['sheets'][$sheetName]['ROOT']['el']['settings.' . $fieldName]);
		}
	}
}