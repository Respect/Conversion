<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Types\Table;

class Col extends AbstractTrColInteraction implements ColBindInterface, TrBindInterface
{
	public $cols = array();

	public function __construct($col=null, $othercol=null, $etc=null)
	{
		$this->cols = func_get_args();
	}

	public function bindToTableTr(Tr $target)
	{
		return $this->bindTrCol($target, $this);
	}
}