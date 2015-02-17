<?php

namespace Cbrunet\CbNewscal\Tests\Unit\Controller;

class NewscalControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

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
	 * Data provider for testNavigationArray
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
