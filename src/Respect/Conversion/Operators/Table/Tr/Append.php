<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Operators\Table\Td\Callback as TdCallback;
use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\TrSelectInterface;

class Append extends TdCallback implements TrSelectInterface
{	
	public function __construct($string)
	{
		$this->callback = function($v) use ($string) {
			return $v.$string;
		};
	}

	public function transform($target)
	{
		foreach ($this->selector->lines as $tr)
			$this->selector->tds[] = array($tr, null);

		return parent::transform($target);
	}
}