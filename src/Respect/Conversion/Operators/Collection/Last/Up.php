<?php

namespace Respect\Conversion\Operators\Collection\Last;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Up extends AbstractOperator implements ItemSelectInterface
{
	public function transform($target)
	{
		return end($target);
	}
}