<?php

namespace Respect\Conversion\Operators\Collection\Last;

use Respect\Conversion\Selectors\Collection\ItemSelectInterface;
use Respect\Conversion\Operators\Collection\Item\Callback as ItemCallback;

class Callback extends ItemCallback implements ItemSelectInterface
{
	public function transform($target)
	{
		end($target);
		$this->selector->items = array(key($target));
		return parent::transform($target);
	}
}