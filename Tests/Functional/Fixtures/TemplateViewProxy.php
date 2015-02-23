<?php

namespace Cbrunet\CbNewscal\Tests\Functional\Fixtures;

class TemplateViewProxy extends \TYPO3\CMS\Fluid\View\TemplateView {

	public $variables = array();

	public function assign($key, $value) {
		$this->variables[$key] = $value;
	}

	public function assignMultiple(array $values) {
		foreach ($values as $key => $value) {
			$this->assign($key, $value);
		}
	}

}