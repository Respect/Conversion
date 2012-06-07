<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Types\Table;

class Tr extends AbstractTrColInteraction implements TrBindInterface, ColBindInterface
{
	public $lines = array();

	public function __construct($line=null, $otherLine=null, $etc=null)
	{
		$this->lines = func_get_args();
	}

	public function bindToCol(Col $target) 
	{
		return $this->bindTrCol($this, $target);
	}

}