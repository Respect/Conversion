<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Selectors\Common\AbstractSelector;
use Respect\Conversion\Types\Table;

class Td extends AbstractSelector implements TdBindInterface
{
	public $tds = array();

	public function __construct($td=null, $othertd=null, $etc=null)
	{
		$this->tds = array_filter(func_get_args());
	}

	public function bindToTd(Td $target)
	{
		$mirror = new \ReflectionClass(__CLASS__);
		return $mirror->newInstanceArgs(array_merge($this->tds, $target->tds));
	}

}