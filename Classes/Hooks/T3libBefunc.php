<?php

namespace Cbrunet\CbNewscal\Hooks;

class T3libBefunc extends \Tx_News_Hooks_T3libBefunc {

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
		}
	}
}
