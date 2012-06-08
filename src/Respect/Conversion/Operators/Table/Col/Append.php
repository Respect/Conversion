<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Table\Td\Callback as TdCallback;
use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Append extends TdCallback implements ColSelectInterface
{	
	public function __construct($string)
	{			
		$this->callback = function($v) use ($string) {
			return $v.$string;
		};
	}

	public function transform($target)
	{
		foreach ($this->selector->cols as $col)
			$this->selector->tds[] = array(null, $col);

		return parent::transform($target);
	}
}