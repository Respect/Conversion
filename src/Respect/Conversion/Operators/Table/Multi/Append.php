<?php

namespace Respect\Conversion\Operators\Table\Multi;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Common\MultiSelectInterface;

class Append extends AbstractOperator implements MultiSelectInterface
{
	public function transform($input)
	{
		foreach ($this->selector->selectors as $s)
			$input = $s->buildOperator('Append')
					   ->operateUsing($this->table, $s)
					   ->transform($input);

		return $input;
	}

}