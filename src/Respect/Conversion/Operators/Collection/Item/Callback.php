<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Callback extends AbstractCallback implements ItemSelectInterface
{
	public function transform($target)
	{
		$callback = $this->callback;
		
		foreach ($target as $item => &$line)
			foreach ($this->selector->items as $itemSpec)
				if (is_callable($itemSpec) && !$cbResult = $itemSpec($line, $item));
				elseif (is_callable($itemSpec) && $cbResult || $item == $itemSpec)
				    $line = $callback($line);

		return $target;
	}
}