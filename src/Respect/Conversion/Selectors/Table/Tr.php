<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Types\Table;

class Tr implements TrBindInterface
{
	public $lines = array();

	public function __construct($line=null, $otherLine=null, $etc=null)
	{
		$this->lines = func_get_args();
	}

	public function bindToTr(Tr $target)
	{
		$mirror = new \ReflectionClass(__CLASS__);
		return $mirror->newInstanceArgs(array_merge($this->lines, $target->lines));
	}

}