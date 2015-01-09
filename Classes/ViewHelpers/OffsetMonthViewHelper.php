<?php

namespace Cbrunet\CbNewscal\ViewHelpers;

class OffsetMonthViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param int $month Current month
	 * @param int $year Current year
	 * @param int $offset Number of months to add (or remove)
	 * @return string Rendered result
	 */
	public function render($month, $year, $offset=1) {
		$nextdate = mktime(0, 0, 0, $month + $offset, 1, $year);
		$this->templateVariableContainer->add('month', date('m', $nextdate));
		$this->templateVariableContainer->add('year', date('Y', $nextdate));
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove('month');
		$this->templateVariableContainer->remove('year');

		return $output;
	}

}