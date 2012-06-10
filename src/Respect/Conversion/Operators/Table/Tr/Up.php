<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\TrSelectInterface;

class Up extends AbstractOperator implements TrSelectInterface
{	

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
						$newInput[$no] = $line;

		return $newInput;
	}
}