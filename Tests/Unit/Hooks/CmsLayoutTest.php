<?php

namespace Cbrunet\CbNewscal\Tests\Unit\Hooks;

class CmsLayoutTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	public function setUp() {
		$db = $this->getAccessibleMock('TYPO3\\CMS\\Dbal\\Database\\DatabaseConnection',
			array('getFieldInfoCache', 'fullQuoteStr', 'exec_SELECTgetSingleRow',
				  'exec_DELETEquery', 'exec_INSERTquery', 'exec_SELECTgetRows'),
			array(), '', FALSE);
		$mockCacheFrontend = $this->getMock('TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend', array(), array(), '', FALSE);
		$db->expects($this->any())->method('getFieldInfoCache')->will($this->returnValue($mockCacheFrontend));
		$GLOBALS['TYPO3_DB'] = $db;

		$lang = $this->getMock('TYPO3\\CMS\\Lang\\LanguageService');
		$lang->expects($this->any())->method('sL')->willReturn('translation');
		$GLOBALS['LANG'] = $lang;

		$this->fixture = $this->getMock('\\Cbrunet\\CbNewscal\\Hooks\\CmsLayout',
			array('getFieldFromFlexform', 'getStartingPoint', 'getCategorySettings'));
	}

	/**
	 * @test
	 **/
	public function getExtensionSummaryWrongExtensionKey() {
		$params = array(
			'row' => array(
				'list_type' => 'dummy_pi1',
			)
		);
		$this->fixture->expects($this->never())->method('getFieldFromFlexform')->with('switchableControllerActions');

		$result = $this->fixture->getExtensionSummary($params);
		$this->assertEquals('', $result);
	}

	/**
	 * @test
	 **/
	public function getExtensionSummaryNoActionConfigured() {
		$params = array(
			'row' => array(
				'list_type' => 'news_pi1',
			)
		);
		$this->fixture->expects($this->once())->method('getFieldFromFlexform')->with('switchableControllerActions');

		$result = $this->fixture->getExtensionSummary($params);
		$this->assertEquals('translation', $result);
	}

	/**
	 * @test
	 **/
	public function getExtensionSummaryActionNewscalCalendar() {
		$flexform = array(
			'data' => array(
				$sheet => array(
					'lDEF' => array(
						$key => array(
							'vDEF' => $value
						)
					)
				)
			)
		);
		$xml = \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml($flexform);
		$params = array(
			'row' => array(
				'list_type' => 'news_pi1',
				'pi_flexform' => $xml,
			)
		);
		$this->fixture->expects($this->any())->method('getFieldFromFlexform')
			->willReturn('Newscal->calendar;dummy');

		$result = $this->fixture->getExtensionSummary($params);
		$this->assertEquals('<pre>translation</pre><pre style="white-space:normal"><strong>translation</strong> Newscal-&gt;calendar;dummy<br /><strong>translation</strong> Newscal-&gt;calendar;dummy<br /><strong>translation</strong> translation<br /><strong>translation</strong> <br /></pre>', $result);
	}

}
