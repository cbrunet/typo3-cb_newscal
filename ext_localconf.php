<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['Newscal->calendar'] = 'Calendar';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['News']['plugins']['Pi1']['controllers']['Newscal']['actions']['0'] = 'calendar';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Hooks/T3libBefunc.php']['updateFlexforms'][] = 'EXT:cb_newscal/Classes/Hooks/T3libBefunc.php:updateFlexforms';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['news_pi1']['newscal'] = '\Cbrunet\CbNewscal\Hooks\CmsLayout->getExtensionSummary';
