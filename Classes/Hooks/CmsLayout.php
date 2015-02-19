<?php

namespace Cbrunet\CbNewscal\Hooks;

/***************************************************************
 *  Copyright notice
*
*  (c) 2012-2013 Charles Brunet <charles@cbrunet.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Hook to display verbose information about the plugin
 *
 */
class CmsLayout extends \Tx_News_Hooks_CmsLayout {

	public function getExtensionSummary(array $params) {
		$result = $actionTranslationKey = '';

		if ($params['row']['list_type'] == self::KEY . '_pi1') {
			$this->flexformData = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($params['row']['pi_flexform']);

			// if flexform data is found
			$actions = $this->getFieldFromFlexform('switchableControllerActions');
			if (!empty($actions)) {
				$actionList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $actions);

				// translate the first action into its translation
				$actionTranslationKey = strtolower(str_replace('->', '_', $actionList[0]));
				$actionTranslation = $GLOBALS['LANG']->sL('LLL:EXT:cb_newscal/Resources/Private/Language/locallang_db.xlf:flexforms_general.mode.' . $actionTranslationKey);

				$result .= '<pre>' . $actionTranslation . '</pre>';

			} else {
				$result = $GLOBALS['LANG']->sL(self::LLPATH . 'flexforms_general.mode.not_configured');
			}

			if (is_array($this->flexformData)) {

				switch ($actionTranslationKey) {
					case 'newscal_calendar':
						$this->getStartingPoint();
						$this->getTimeRestrictionSetting();
						$this->getTopNewsRestrictionSetting();
						$this->getCategorySettings();
						$this->getArchiveSettings();
						$this->getDetailPidSetting();
						$this->getListPidSetting();
						$this->getTagRestrictionSetting();
						break;
				}

				// for all views
				$this->getOverrideDemandSettings();
				$this->getTemplateLayoutSettings($params['row']['pid']);

				$result .= $this->renderSettingsAsTable();
			}
		}

		return $result;
	}

}