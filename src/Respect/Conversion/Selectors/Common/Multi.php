<?php

namespace Respect\Conversion\Selectors\Common;

use Respect\Conversion\Types\Table;
use Respect\Conversion\Selectors;

class Multi
{
	public $selectors = array();

	public function __construct($selectorOne = null, $selectorTwo = null, $etc = null)
	{
		$this->selectors = func_get_args();
	}
}