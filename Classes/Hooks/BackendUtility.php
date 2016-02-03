<?php

namespace Cbrunet\CbNewscal\Hooks;

class BackendUtility extends \GeorgRinger\News\Hooks\BackendUtility {

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

    protected $monthsBefore = <<<EOT
                    <settings.monthsBefore>
                        <TCEforms>
                        <label>LLL:EXT:cb_newscal/Resources/Private/Language/locallang_be.xlf:flexforms_general.monthsBefore</label>
                            <config>
                                <default></default>
                                <type>input</type>
                                <size>5</size>
                                <eval>num</eval>
                            </config>
                        </TCEforms>
                    </settings.monthsBefore>
EOT;

    protected $monthsAfter = <<<EOT
                    <settings.monthsAfter>
                        <TCEforms>
                        <label>LLL:EXT:cb_newscal/Resources/Private/Language/locallang_be.xlf:flexforms_general.monthsAfter</label>
                            <config>
                                <default></default>
                                <type>input</type>
                                <size>5</size>
                                <eval>num</eval>
                            </config>
                        </TCEforms>
                    </settings.monthsAfter>
EOT;

    protected $eventRestrictionField = '<settings.eventRestriction>
                        <TCEforms>
                            <label>LLL:EXT:eventnews/Resources/Private/Language/locallang.xlf:flexforms_general.eventRestriction</label>
                            <config>
                                <type>select</type>
                                <items>
                                    <numIndex index="0" type="array">
                                        <numIndex index="0">LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:flexforms_general.no-constraint</numIndex>
                                        <numIndex index="1"></numIndex>
                                    </numIndex>
                                    <numIndex index="1">
                                        <numIndex index="0">LLL:EXT:eventnews/Resources/Private/Language/locallang.xlf:flexforms_general.eventRestriction.1</numIndex>
                                        <numIndex index="1">1</numIndex>
                                    </numIndex>
                                    <numIndex index="2">
                                        <numIndex index="0">LLL:EXT:eventnews/Resources/Private/Language/locallang.xlf:flexforms_general.eventRestriction.2</numIndex>
                                        <numIndex index="1">2</numIndex>
                                    </numIndex>
                                </items>
                            </config>
                        </TCEforms>
                    </settings.eventRestriction>';

    /**
     * Remove unused fields in the flexform.
     */
    public function updateFlexforms(&$params, &$reference) {
        switch ($params['selectedView']) {
            case 'News->calendar':
                $this->updateCalendarFlexforms($params, $reference);
                break;
        }
    }

    protected function updateCalendarFlexforms(&$params, &$reference) {
        $removedFields = array(
            'sDEF' => 'orderBy,orderDirection,singleNews',
            'additional' => 'limit,offset,excludeAlreadyDisplayedNews,disableOverrideDemand,list.paginate.itemsPerPage',
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
                                                               
        $monthsBefore = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->monthsBefore);
        $monthsAfter = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->monthsAfter);
        if (!is_array($params['dataStructure']['sheets']['template']['ROOT']['el'])) {
            // This should never happen...
            $params['dataStructure']['sheets']['template']['ROOT']['el'] = array();
        }
        $params['dataStructure']['sheets']['template']['ROOT']['el'] = array_merge(array('settings.monthsBefore' => $monthsBefore, 'settings.monthsAfter' => $monthsAfter),
            $params['dataStructure']['sheets']['template']['ROOT']['el']);

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews')) {
            $eventRestrictionXml = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->eventRestrictionField);
            $params['dataStructure']['sheets']['sDEF']['ROOT']['el'] = $params['dataStructure']['sheets']['sDEF']['ROOT']['el'] + array(
                    'settings.eventRestriction' => $eventRestrictionXml);
        }
    }
}
