<?php

namespace Respect\Conversion\Selectors\Chart;

use Respect\Conversion\Types\Chart;
use Respect\Conversion\Selectors\Common\AbstractSelector;

class RowSeries extends AbstractSelector implements RowSeriesBindInterface
{
	public $series = array();

	public function __construct($series=null, $otherseries=null, $etc=null)
	{
		$this->series = func_get_args();
	}

	public function bindToChartRowSeries(RowSeries $target)
	{
		$mirror = new \ReflectionClass(__CLASS__);
		return $mirror->newInstanceArgs(array_merge($this->series, $target->series));
	}
}