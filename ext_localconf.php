<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['Newscal->calendar'] = 'LLL:EXT:cb_newscal/Resources/Private/Language/locallang_db.xlf:flexforms_general.mode.newscal_calendar';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['News']['plugins']['Pi1']['controllers']['Newscal']['actions']['0'] = 'calendar';

// Display custom information for Preview in Page module
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['news_pi1']['newscal'] = 'Cbrunet\CbNewscal\Hooks\CmsLayout->getExtensionSummary';

// Update fields in the flexform
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Hooks/T3libBefunc.php']['updateFlexforms'][] = 'Cbrunet\CbNewscal\Hooks\T3libBefunc->updateFlexforms';
