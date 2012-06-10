<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Name extends AbstractOperator implements ItemSelectInterface
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function transform($target)
	{
		$newTarget = array();

		foreach ($target as $item => $line)
			foreach ($this->selector->items as $itemSpec)
				if (is_callable($itemSpec) && !$cbResult = $itemSpec($line, $item))
					$newTarget[$item] = $line;
				elseif ($item == $itemSpec || (is_callable($itemSpec) && $cbResult))
			    	$newTarget[$this->name] = $line;
				else
					$newTarget[$item] = $line;

		return $newTarget;
	}
}