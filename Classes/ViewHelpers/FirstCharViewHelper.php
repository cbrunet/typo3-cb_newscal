<?php

namespace Cbrunet\CbNewscal\ViewHelpers;

class FirstCharViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	public function render() {
		$string = trim($this->renderChildren());
		return $string[0];
	}

}