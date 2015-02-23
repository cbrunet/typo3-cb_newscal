<?php

namespace Cbrunet\CbNewscal\Tests\Functional\Controller;

class NewscalControllerEventTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager */
	protected $objectManager;

	/** @var  Tx_News_Domain_Repository_NewsRepository */
	protected $newsRepository;

	/** @var  \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface */
	protected $configurationManager;

	/** @var  \Cbrunet\CbNewscal\Controller\NewscalController */
	protected $controller;

	protected $coreExtensionsToLoad = array('fluid');

	/**
	 * @var array
	 */
	protected $testExtensionsToLoad = array(
		'typo3conf/ext/news',
		'typo3conf/ext/roq_newsevent',
		'typo3conf/ext/cb_newscal',
	);

	public function setUp() {
		parent::setUp();

		$this->importDataSet(__DIR__ . '/../Fixtures/news.xml');
		$this->importDataSet(__DIR__ . '/../Fixtures/events.xml');
		$this->importDataSet(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:core/Tests/Functional/Fixtures/pages.xml'));
		
		$rnesetup = 'EXT:roq_newsevent/Configuration/TypoScript/setup.txt';
		$this->setUpFrontendRootPage(1, array($rnesetup));
		
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->newsRepository = $this->objectManager->get('Tx_News_Domain_Repository_NewsRepository');
		$this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$this->controller = $this->getAccessibleMock('Cbrunet\\CbNewscal\\Controller\\NewscalController', array("dummy"));
		$this->controller->injectObjectManager($this->objectManager);
		$this->controller->injectConfigurationManager($this->configurationManager);
		$this->controller->injectNewsRepository($this->newsRepository);
		$this->view = $this->objectManager->get('Cbrunet\\CbNewscal\\Tests\\Functional\\Fixtures\\TemplateViewProxy');
		$this->controller->setView($this->view);


	}

	/**
	 * @test
	 * @dataProvider dataProvider
	 **/
	public function testMonthsArray($settings, $expected) {
		$cs = $this->controller->_get('settings');
		if (is_array($cs))
			$settings = array_merge($cs, $settings);
		$this->controller->_set('settings', $settings);

		$this->controller->calendarAction();

		foreach($this->view->variables['months'] as $mkey => $month) {
			foreach($month['weeks'] as $wkey => $week) {
				foreach($week as $dkey => $day) {
					if (isset($expected[$mkey][$wkey][$dkey])) {
						foreach($expected[$mkey][$wkey][$dkey] as $key => $value) {
							if ($key == 'news') {
								foreach($value as $idx => $news) {
									foreach ($news as $attr=>$expval) {
										$val = call_user_func(array($day['news'][$idx], 'get' . ucfirst($attr)));
										$this->assertEquals($expval, $val);
									}
								}
							}
							else {
								$this->assertEquals($value, $day[$key]);
							}
						}
					}
					else {
						$this->assertEmpty($day['news'],
							$mkey . ',' . $wkey . ',' . $dkey);
					}
				}
			}
		}
	}

	/**
	 * @return array
	 **/
	public function dataProvider() {
		/**************************
		 *    0  1  2  3  4  5  6 *
		 *   ---------------------*
		 *  | S  M  T  W  T  F  S *
		 * 0| 1  2  3  4  5  6  7 *
		 * 1| 8  9 10 11 12 13 14 *
		 * 2|15 16 17 18 19 20 21 *
		 * 3|22 23 24 25 26 27 28 *
		 **************************/
		return array(
			'events' => array(
				array(
					'displayMonth' => '2015-02',
					'firstDayOfWeek' => 0,
					'dateField' => 'eventStartdate',
				),
				array(
					0 => array(
						0 => array(
							0 => array(
								'news' => array(
									0 => array(
										'title' => 'event 4',
									),
								),
								'startev' => False,
								'endev' => True,
							),
						),
						1 => array(
							6 => array(
								'news' => array(
									0 => array(
										'title' => 'event 1',
									),
								),
								'startev' => True,
								'endev' => True,
							),
						),
						2 => array(
							2 => array(
								'news' => array(
									0 => array(
										'title' => 'event 3',
									),
								),
								'startev' => True,
								'endev' => False,
							),
							3 => array(
								'news' => array(
									0 => array(
										'title' => 'event 3',
									),
								),
								'startev' => False,
								'endev' => True,
							),
						),
						3 => array(
							3 => array(
								'news' => array(
									0 => array(
										'title' => 'event 2',
									),
								),
								'startev' => True,
								'endev' => True,
							),
							6 => array(
								'news' => array(
									0 => array(
										'title' => 'event 5',
									),
								),
								'startev' => True,
								'endev' => False,
							),
						),
					),
				)
			),
		);
	}

}