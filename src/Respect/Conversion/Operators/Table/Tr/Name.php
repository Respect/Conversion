<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\TrSelectInterface;

class Name extends AbstractOperator implements TrSelectInterface
{	

	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function transform($input)
	{
		$lines = $this->selector->lines;
		$name = $this->name;

		$newInput = array();

		foreach ($input as $no => $line)
			if (!empty($lines))
				foreach ($lines as $lineSpec)
					if ($no == $lineSpec
						|| is_callable($lineSpec) && $lineSpec($no))
						$newInput[$name] = $line;
					else 
						$newInput[$no] = $line;

		return array_filter($newInput);
	}
}