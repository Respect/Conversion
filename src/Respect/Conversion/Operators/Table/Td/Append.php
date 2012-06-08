<?php

namespace Respect\Conversion\Operators\Table\Td;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\TdSelectInterface;

class Append extends Callback implements TdSelectInterface
{	
	public function __construct($string)
	{
		$this->callback = function($v) use ($string) {
			return $v.$string;
		};
	}

}