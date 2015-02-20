<?php

namespace Cbrunet\CbNewscal\Tests\Unit;

class UnitTestCase extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	public function mockDatabase() {
		if (isset($GLOBALS['TYPO3_DB'])) {
			return;
		}
		$db = $this->getAccessibleMock('TYPO3\\CMS\\Dbal\\Database\\DatabaseConnection',
			array('getFieldInfoCache', 'fullQuoteStr', 'exec_SELECTgetSingleRow',
				  'exec_DELETEquery', 'exec_INSERTquery', 'exec_SELECTgetRows'),
			array(), '', FALSE);
		$this->mockCacheFrontend = $this->getMock('TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend', array(), array(), '', FALSE);
		$db->expects($this->any())->method('getFieldInfoCache')
			->will($this->returnValue($this->mockCacheFrontend));
		$GLOBALS['TYPO3_DB'] = $db;
	}

	public function cleanupDatabase() {
		if (isset($this->mockCacheFrontend)) {
			unset($GLOBALS['TYPO3_DB'], $this->mockCacheFrontend);
		}
	}

}
