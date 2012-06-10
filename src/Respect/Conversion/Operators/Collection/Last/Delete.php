<?php

namespace Respect\Conversion\Operators\Collection\Last;

use Respect\Conversion\Selectors\Collection\ItemSelectInterface;
use Respect\Conversion\Operators\Collection\Item\Delete as ItemDelete;

class Delete extends ItemDelete implements ItemSelectInterface
{
	public function transform($target)
	{
		end($target);
		$this->selector->items = array(key($target));
		return parent::transform($target);
	}
}