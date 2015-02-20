<?php

namespace Cbrunet\CbNewscal\Tests\Unit\Hooks;

class T3libBefuncTest extends \Cbrunet\CbNewscal\Tests\Unit\UnitTestCase {

	public function setUp() {
		$this->mockDatabase();

		$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('news',
			'/Configuration/FlexForms/flexform_news.xml');
		$xmlflex = file_get_contents($path);
		$this->flexform = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($xmlflex);
	}

	public function tearDown() {
		$this->cleanupDatabase();
	}

	/**
	 * @test
	 **/
	public function updateFlexforms() {
		$fixture = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Cbrunet\\CbNewscal\\Hooks\\T3libBefunc');
		$params = array(
			'selectedView' => 'Newscal->calendar',
			'dataStructure' => $this->flexform);
		$reference = array();

		$this->assertNotNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.orderBy']);
		$this->assertNotNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.orderDirection']);
		$this->assertNotNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.singleNews']);

		$fixture->updateFlexforms($params, $reference);

		$this->assertNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.orderBy']);
		$this->assertNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.orderDirection']);
		$this->assertNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.singleNews']);
		$this->assertNull($params['dataStructure']['sheets']['additional']['ROOT']['el']['settings.limit']);
		$this->assertNull($params['dataStructure']['sheets']['additional']['ROOT']['el']['settings.offset']);
		$this->assertNull($params['dataStructure']['sheets']['additional']['ROOT']['el']['settings.excludeAlreadyDisplayedNews']);
		$this->assertNull($params['dataStructure']['sheets']['additional']['ROOT']['el']['settings.disableOverrideDemand']);
		$this->assertNull($params['dataStructure']['sheets']['additional']['ROOT']['el']['settings.list.paginate.itemsPerPage']);
		
		$this->assertNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'][0]);
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('roq_newsevent')) {
			$this->assertEquals('eventStartdate', end($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'])[1]);
		}
		else {
			$this->assertEquals('crdate', end($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.dateField']['TCEforms']['config']['items'])[1]);

		}

		$this->assertNotNull($params['dataStructure']['sheets']['sDEF']['ROOT']['el']['settings.displayMonth']);
		$this->assertNotNull($params['dataStructure']['sheets']['template']['ROOT']['el']['settings.monthsBefore']);
		$this->assertNotNull($params['dataStructure']['sheets']['template']['ROOT']['el']['settings.monthsAfter']);


	}

}
