<?php

namespace Cbrunet\CbNewscal\Tests\Unit\Controller;

class NewsControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * Test calendar action
	 *
	 * @test
	 **/
	public function calendarAction() {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')
			->willReturn(array('monthsBefore' => 1, 'monthsAfter' => 2));

		$fixture = $this->getMock(
			'Cbrunet\\CbNewscal\\Controller\\NewsController',
			array('createDemandObject', 'getWeeks', 'createNavigationArray')
		);
		$fixture->injectConfigurationManager($configurationManager);
		$fixture->expects($this->exactly(4))->method('getWeeks');

		$newsRepository = $this->getMock(
			'\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository', array(), array(), '', FALSE
		);
		$fixture->injectNewsRepository($newsRepository);
		$fixture->setView($this->getMock('TYPO3\\CMS\\Fluid\\View\\TemplateView', array(), array(), '', FALSE));


		$fixture->calendarAction();
	}

	/**
	 * Test calendar action (for event)
	 *
	 * @test
	 **/
	public function calendarActionEvent() {
		if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews')) {
			$this->markTestSkipped('The eventnews extension is not available.');
		}

		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn(array('dateField' => 'datetime'));
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		// $objectManager->expects($this->once())
		// 	->method('get')->with('\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository');
		$newsRepository = $this->getMock(
			'\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository', array(), array(), '', FALSE
		);
		$fixture = $this->getAccessibleMock(
			'Cbrunet\\CbNewscal\\Controller\\NewsController',
			array('createDemandObject', 'getWeeks', 'createNavigationArray')
		);

		$fixture->injectConfigurationManager($configurationManager);
		$fixture->injectNewsRepository($newsRepository);
		$fixture->setView($this->getMock('TYPO3\\CMS\\Fluid\\View\\TemplateView', array(), array(), '', FALSE));
		$fixture->_set('objectManager', $objectManager);


		$fixture->calendarAction();
	}

	/**
	 * Test creation of the demand object against different settings
	 *
	 * @test
	 * @dataProvider createDemandObjectDataProvider
	 * @return void
	 */
	public function createDemandObject($settings, $overwriteDemand, $expMonth, $expYear) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$mockedController = $this->getAccessibleMock('Cbrunet\\CbNewscal\\Controller\\NewsController',
			array('createDemandObjectFromSettings'));
		$mockedController->injectConfigurationManager($configurationManager);

		$d = clone new \Tx_News_Domain_Model_Dto_AdministrationDemand();
		$mockedController->method('createDemandObjectFromSettings')->willReturn($d);
		$demand = $mockedController->_call('createDemandObject', $overwriteDemand);

		$this->assertEquals($expMonth, $mockedController->_get('month'));
		$this->assertEquals($expYear, $mockedController->_get('year'));
		$this->assertNull($demand->getMonth());
		$this->assertNull($demand->getYear());
	}

	/**
	 * Data provider createDemandObject
	 *
	 * @return array
	 */
	public function createDemandObjectDataProvider() {
		$po = mktime(0, 0, 0, date('n')+1, 1, date('Y'));
		$mo = mktime(0, 0, 0, date('n')-1, 1, date('Y'));

		return array(
			'base' => array(array(), NULL, date('n'), date('Y')),
			'overwrite' => array(array(), array('month' => 12, 'year' => 2014), 12, 2014),
			'absolute' => array(array('displayMonth' => '2010-11'), NULL, 11, 2010),
			'relativePlusOne' => array(array('displayMonth' => '+1'), NULL, date('n', $po), date('Y', $po)),
			'relativeMinusOne' => array(array('displayMonth' => '-1'), NULL, date('n', $mo), date('Y', $mo)),
			'absoluteButOverwrite' => array(array('displayMonth' => '2010-11'), array('month' => 12, 'year' => 2014), 12, 2014),
		);
	}

	/**
	 * Test getWeeks function
	 *
	 * @test
	 * @dataProvider getWeeksDataProvider
	 * @return void
	 */
	public function getWeeks($month, $year, $settings, $expected) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$newsRepository = $this->getMockBuilder('\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository')
			->disableOriginalConstructor()
            ->getMock();
        $newsRepository->method('findDemanded')->willReturn(array());

		$mockedController = $this->getAccessibleMock('Cbrunet\\CbNewscal\\Controller\\NewsController', array('dummy'));
		$mockedController->injectConfigurationManager($configurationManager);
		$mockedController->injectNewsRepository($newsRepository);

		$d = clone new \GeorgRinger\News\Domain\Model\Dto\AdministrationDemand();
		$weeks = $mockedController->_call('getWeeks', $d, $month, $year);

		$this->assertEquals($expected[0], $weeks[0][0]['day']);
		$this->assertEquals($expected[1], end(end($weeks))['day']);
	}

	/**
	 * Data provider for getWeeks
	 *
	 * @return array
	 */
	public function getWeeksDataProvider() {
		return array(
			'feb2015sun' => array( 2, 2015, array('firstDayOfWeek' => 0), array(1, 28)),
			'dec2014sun' => array(12, 2014, array('firstDayOfWeek' => 0), array(30, 3)),
		);
	}

	/**
	 * Test getWeeks with event. Verify start and end ev flags.
	 *
	 * @test
	 **/
	public function getWeekStartEndEv() {
		if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews')) {
			$this->markTestSkipped('The eventnews extension is not available.');
		}

		$settings = array(
			'firstDayOfWeek' => 0,
			'dateField' => 'datetime'
		);

		$event = $this->getMock('GeorgRinger\\Eventnews\\Domain\\Model\\News');
		$event->method('getDatetime')->willReturn(new \DateTime('2014-12-24'));
		$event->method('getEventEnd')->willReturn(new \DateTime('2014-12-26'));

		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$newsRepository = $this->getMockBuilder('\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository')
			->disableOriginalConstructor()
            ->getMock();
        $newsRepository->method('findDemanded')->willReturn(array($event));

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewsController', array('dummy'));
		$mockedController->injectConfigurationManager($configurationManager);
		$mockedController->injectNewsRepository($newsRepository);

		$demand = $this->getMock('GeorgRinger\\News\\Domain\\Model\\Dto\\AdministrationDemand');
		$demand->method('getDateField')->willReturn("datetime");
		$weeks = $mockedController->_call('getWeeks', $demand, 12, 2014);

		$this->assertTrue($weeks[3][3]['startev']);  // 24
		$this->assertFalse($weeks[3][3]['endev']);  // 24
		$this->assertFalse($weeks[3][4]['startev']);  // 25
		$this->assertFalse($weeks[3][4]['endev']);  // 25
		$this->assertFalse($weeks[3][5]['startev']);  // 26
		$this->assertTrue($weeks[3][5]['endev']);  // 26
	}



	/**
	 * Test two events the same day.
	 *
	 * @test
	 **/
	public function twoEventsSameDay() {
		$settings = array(
			'firstDayOfWeek' => 0,
			'dateField' => 'datetime'
		);

		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('eventnews')) {
			$event = $this->getMock('GeorgRinger\\Eventnews\\Domain\\Model\\News');
		}
		else
		{
			$event = $this->getMock('GeorgRinger\\News\\Domain\\Model\\News');
		}

		$event->method('getDatetime')->willReturn(new \DateTime('2014-12-24'));
		$event->method('getEventEnd')->willReturn(new \DateTime('2014-12-24'));

		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$newsRepository = $this->getMockBuilder('\\GeorgRinger\\News\\Domain\\Repository\\NewsRepository')
			->disableOriginalConstructor()
            ->getMock();
        $newsRepository->method('findDemanded')->willReturn(array($event, $event));

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewsController', array('dummy'));
		$mockedController->injectConfigurationManager($configurationManager);
		$mockedController->injectNewsRepository($newsRepository);

		$demand = $this->getMock('GeorgRinger\\News\\Domain\\Model\\Dto\\AdministrationDemand');
		$demand->method('getDateField')->willReturn("datetime");
		$weeks = $mockedController->_call('getWeeks', $demand, 12, 2014);

		$this->assertEquals(2, sizeof($weeks[3][3]['news']));
	}

	/**
	 * Test calculation of the fist day of the monthly calendar
	 *
	 * @test
	 * @dataProvider firstDayOfMonthDataProvider
	 * @return void
	 */
	public function firstDayOfMonth($month, $year, $firstDayOfWeek, $expected) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn(array('firstDayOfWeek' => $firstDayOfWeek));

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewsController', array('dummy'));
		$mockedController->injectConfigurationManager($configurationManager);
		
		$this->assertEquals($expected, $mockedController->_call('firstDayOfMonth', $month, $year));
	}

	/**
	 * Data provider firstDayOfMonth
	 *
	 * @return array
	 */
	public function firstDayOfMonthDataProvider() {
		return array(
			'sunFirstFeb2015' => array(2, 2015, 0, 1),
			'sunFirstDec2014' => array(12, 2014, 0, 0),
			'sunFirstJul2014' => array(7, 2014, 0, -1),
			'sunFirstJan2014' => array(1, 2014, 0, -2),
			'sunFirstJan2015' => array(1, 2015, 0, -3),
			'sunFirstAug2014' => array(8, 2014, 0, -4),
			'sunFirstMar2014' => array(3, 2014, 0, -5),
			'monFirstFeb2015' => array(2, 2015, 1, -5),
			'monFirstDec2014' => array(12, 2014, 1, 1),
			'monFirstJul2014' => array(7, 2014, 1, 0),
			'monFirstJan2014' => array(1, 2014, 1, -1),
			'monFirstJan2015' => array(1, 2015, 1, -2),
			'monFirstAug2014' => array(8, 2014, 1, -3),
			'monFirstMar2014' => array(3, 2014, 1, -4),
		);
	}

	/**
	 * Test construction of navigation from different settings
	 *
	 * @test
	 * @dataProvider navigationArrayDataProvider
	 * @return void
	 */
	public function navigationArray($month, $year, $settings, $expected) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$mockedController = $this->getAccessibleMock('Cbrunet\\CbNewscal\\Controller\\NewsController', array('dummy'));
		$mockedController->_set('month', $month);
		$mockedController->_set('year', $year);
		$mockedController->injectConfigurationManager($configurationManager);
		$nav = $mockedController->_call('createNavigationArray');

		$this->assertEquals($nav, $expected);
	}

	/**
	 * Data provider for navigationArray
	 *
	 * @return array
	 */
	public function navigationArrayDataProvider() {
		$oneMonthKeepOne = array(
			'monthsBefore' => 0, 
			'monthsAfter' => 0, 
			'scrollMode' => -1,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');
		$oneMonthScrollAll = array(
			'monthsBefore' => 0, 
			'monthsAfter' => 0, 
			'scrollMode' => 0,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');
		$oneMonthScrollOne = array(
			'monthsBefore' => 0, 
			'monthsAfter' => 0, 
			'scrollMode' => 1,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');
		$threeMonthsKeepOne = array(
			'monthsBefore' => 1, 
			'monthsAfter' => 1, 
			'scrollMode' => -1,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');
		$threeMonthsScrollAll = array(
			'monthsBefore' => 1, 
			'monthsAfter' => 1, 
			'scrollMode' => 0,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');
		$threeMonthsScrollOne = array(
			'monthsBefore' => 1, 
			'monthsAfter' => 1, 
			'scrollMode' => 1,
			'timeRestriction' => '2010-1-1',
			'timeRestrictionHigh' => '2020-1-1');

		return array(
			'oneMonthKeepOneJan' => array(1, 2015, $oneMonthKeepOne, array(
				'prev' => array('month' => 12, 'year' => 2014), 
				'next' => array('month' => 2, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthKeepOneDec' => array(12, 2015, $oneMonthKeepOne, array(
				'prev' => array('month' => 11, 'year' => 2015), 
				'next' => array('month' => 1, 'year' => 2016), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthKeepOneMar' => array(3, 2015, $oneMonthKeepOne, array(
				'prev' => array('month' =>  2, 'year' => 2015), 
				'next' => array('month' => 4, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollAllJan' => array(1, 2015, $oneMonthScrollAll, array(
				'prev' => array('month' => 12, 'year' => 2014), 
				'next' => array('month' => 2, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollAllDec' => array(12, 2015, $oneMonthScrollAll, array(
				'prev' => array('month' => 11, 'year' => 2015), 
				'next' => array('month' => 1, 'year' => 2016), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollAllMar' => array(3, 2015, $oneMonthScrollAll, array(
				'prev' => array('month' =>  2, 'year' => 2015), 
				'next' => array('month' => 4, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollOneJan' => array(1, 2015, $oneMonthScrollOne, array(
				'prev' => array('month' => 12, 'year' => 2014), 
				'next' => array('month' => 2, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollOneDec' => array(12, 2015, $oneMonthScrollOne, array(
				'prev' => array('month' => 11, 'year' => 2015), 
				'next' => array('month' => 1, 'year' => 2016), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthScrollOneMar' => array(3, 2015, $oneMonthScrollOne, array(
				'prev' => array('month' =>  2, 'year' => 2015), 
				'next' => array('month' => 4, 'year' => 2015), 
				'numberOfMonths' => 1,
				'uid' => 0)),
			'threeMonthsKeepOneJan' => array(1, 2015, $threeMonthsKeepOne, array(
				'prev' => array('month' => 11, 'year' => 2014), 
				'next' => array('month' => 3, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsKeepOneDec' => array(12, 2015, $threeMonthsKeepOne, array(
				'prev' => array('month' => 10, 'year' => 2015), 
				'next' => array('month' => 2, 'year' => 2016), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsKeepOneMar' => array(3, 2015, $threeMonthsKeepOne, array(
				'prev' => array('month' =>  1, 'year' => 2015), 
				'next' => array('month' => 5, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollAllJan' => array(1, 2015, $threeMonthsScrollAll, array(
				'prev' => array('month' => 10, 'year' => 2014), 
				'next' => array('month' => 4, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollAllDec' => array(12, 2015, $threeMonthsScrollAll, array(
				'prev' => array('month' =>  9, 'year' => 2015), 
				'next' => array('month' => 3, 'year' => 2016), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollAllMar' => array(3, 2015, $threeMonthsScrollAll, array(
				'prev' => array('month' => 12, 'year' => 2014), 
				'next' => array('month' => 6, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollOneJan' => array(1, 2015, $threeMonthsScrollOne, array(
				'prev' => array('month' => 12, 'year' => 2014), 
				'next' => array('month' => 2, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollOneDec' => array(12, 2015, $threeMonthsScrollOne, array(
				'prev' => array('month' => 11, 'year' => 2015), 
				'next' => array('month' => 1, 'year' => 2016), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'threeMonthsScrollOneMar' => array(3, 2015, $threeMonthsScrollOne, array(
				'prev' => array('month' =>  2, 'year' => 2015), 
				'next' => array('month' => 4, 'year' => 2015), 
				'numberOfMonths' => 3,
				'uid' => 0)),
			'oneMonthNoPrev' => array(1, 2010, $oneMonthScrollOne, array(
				'prev' => NULL,
				'next' => array('month' => 2, 'year' => 2010),
				'numberOfMonths' => 1,
				'uid' => 0)),
			'oneMonthNoNext' => array(1, 2020, $oneMonthScrollOne, array(
				'prev' => array('month' => 12, 'year' => 2019),
				'next' => NULL,
				'numberOfMonths' => 1,
				'uid' => 0)),
		);
	}

}
