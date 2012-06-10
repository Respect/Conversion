<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Up extends AbstractOperator implements ItemSelectInterface
{
	public function transform($target)
	{
		$newTarget = array();

		foreach ($target as $item => $line)
			foreach ($this->selector->items as $itemSpec)
				if (is_callable($itemSpec) && !$cbResult = $itemSpec($line, $item));
				elseif (is_callable($itemSpec) && $cbResult || $item == $itemSpec)
				    $newTarget[$item] = $line;

		return count($newTarget) == 1 ? current($newTarget) : $newTarget;
	}
}