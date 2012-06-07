<?php

namespace Respect\Conversion\Operators\Table\Td;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\TdSelectInterface;

class Delete extends Callback implements TdSelectInterface
{	
	public function __construct()
	{
		$this->callback = function() {
			return null;	
		};
	}
}