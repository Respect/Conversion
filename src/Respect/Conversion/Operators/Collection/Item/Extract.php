<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Extract extends AbstractOperator implements ItemSelectInterface
{
	public function transform($target)
	{	
		$newTarget = array();

		foreach ($target as $line => $item)
			foreach ($this->selector->items as $itemSpec)
				if ($itemSpec == $line && is_array($item))
					foreach ($item as $key => $value)
						if (!isset($newTarget[$key]))
							$newTarget[$key] = $value;
						else
							$newTarget[] = $value;
				elseif (is_string($line) && !isset($newTarget[$line]))
					$newTarget[$line] = $item;
				elseif (is_numeric($line))
					$newTarget[] = $item;

		return $newTarget;
	}
}