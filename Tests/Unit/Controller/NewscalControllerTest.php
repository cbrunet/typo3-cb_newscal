<?php

namespace Cbrunet\CbNewscal\Tests\Unit\Controller;

class NewscalControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

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

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewscalController',
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
	 * Test calculation of the fist day of the monthly calendar
	 *
	 * @test
	 * @dataProvider firstDayOfMonthDataProvider
	 * @return void
	 */
	public function firstDayOfMonth($month, $year, $firstDayOfWeek, $expected) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn(array('firstDayOfWeek' => $firstDayOfWeek));

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewscalController', array('dummy'));
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
	public function navigationArray($month, $year, $settings, $prevMonth, $prevYear, $nextMonth, $nextYear, $numMonths) {
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager->method('getConfiguration')->willReturn($settings);

		$mockedController = $this->getAccessibleMock('\\Cbrunet\\CbNewscal\\Controller\\NewscalController', array('dummy'));
		$mockedController->_set('month', $month);
		$mockedController->_set('year', $year);
		$mockedController->injectConfigurationManager($configurationManager);
		$nav = $mockedController->_call('createNavigationArray');

		$this->assertEquals($prevMonth, $nav['prev']['month']);
		$this->assertEquals($prevYear, $nav['prev']['year']);
		$this->assertEquals($nextMonth, $nav['next']['month']);
		$this->assertEquals($nextYear, $nav['next']['year']);
		$this->assertEquals($numMonths, $nav['numberOfMonths']);
	}

	/**
	 * Data provider for navigationArray
	 *
	 * @return array
	 */
	public function navigationArrayDataProvider() {
		$oneMonthKeepOne = array('monthsBefore' => 0, 'monthsAfter' => 0, 'scrollMode' => -1);
		$oneMonthScrollAll = array('monthsBefore' => 0, 'monthsAfter' => 0, 'scrollMode' => 0);
		$oneMonthScrollOne = array('monthsBefore' => 0, 'monthsAfter' => 0, 'scrollMode' => 1);
		$threeMonthsKeepOne = array('monthsBefore' => 1, 'monthsAfter' => 1, 'scrollMode' => -1);
		$threeMonthsScrollAll = array('monthsBefore' => 1, 'monthsAfter' => 1, 'scrollMode' => 0);
		$threeMonthsScrollOne = array('monthsBefore' => 1, 'monthsAfter' => 1, 'scrollMode' => 1);

		return array(
			'oneMonthKeepOneJan' => array(1, 2015, $oneMonthKeepOne, 12, 2014, 2, 2015, 1),
			'oneMonthKeepOneDec' => array(12, 2015, $oneMonthKeepOne, 11, 2015, 1, 2016, 1),
			'oneMonthKeepOneMar' => array(3, 2015, $oneMonthKeepOne, 2, 2015, 4, 2015, 1),
			'oneMonthScrollAllJan' => array(1, 2015, $oneMonthScrollAll, 12, 2014, 2, 2015, 1),
			'oneMonthScrollAllDec' => array(12, 2015, $oneMonthScrollAll, 11, 2015, 1, 2016, 1),
			'oneMonthScrollAllMar' => array(3, 2015, $oneMonthScrollAll, 2, 2015, 4, 2015, 1),
			'oneMonthScrollOneJan' => array(1, 2015, $oneMonthScrollOne, 12, 2014, 2, 2015, 1),
			'oneMonthScrollOneDec' => array(12, 2015, $oneMonthScrollOne, 11, 2015, 1, 2016, 1),
			'oneMonthScrollOneMar' => array(3, 2015, $oneMonthScrollOne, 2, 2015, 4, 2015, 1),
			'threeMonthsKeepOneJan' => array(1, 2015, $threeMonthsKeepOne, 11, 2014, 3, 2015, 3),
			'threeMonthsKeepOneDec' => array(12, 2015, $threeMonthsKeepOne, 10, 2015, 2, 2016, 3),
			'threeMonthsKeepOneMar' => array(3, 2015, $threeMonthsKeepOne, 1, 2015, 5, 2015, 3),
			'threeMonthsScrollAllJan' => array(1, 2015, $threeMonthsScrollAll, 10, 2014, 4, 2015, 3),
			'threeMonthsScrollAllDec' => array(12, 2015, $threeMonthsScrollAll, 9, 2015, 3, 2016, 3),
			'threeMonthsScrollAllMar' => array(3, 2015, $threeMonthsScrollAll, 12, 2014, 6, 2015, 3),
			'threeMonthsScrollOneJan' => array(1, 2015, $threeMonthsScrollOne, 12, 2014, 2, 2015, 3),
			'threeMonthsScrollOneDec' => array(12, 2015, $threeMonthsScrollOne, 11, 2015, 1, 2016, 3),
			'threeMonthsScrollOneMar' => array(3, 2015, $threeMonthsScrollOne, 2, 2015, 4, 2015, 3),
		);
	}

}
