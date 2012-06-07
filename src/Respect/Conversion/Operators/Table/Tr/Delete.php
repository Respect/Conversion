<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\TrSelectInterface;

class Delete extends AbstractOperator implements TrSelectInterface
{	
	public function transform($input)
	{
		$lines = $this->selector->lines;

		array_walk($input, function(&$line, $no) use ($lines) {

			if (empty($lines))
				$line = null;
			else
				foreach ($lines as $lineSpec)
					if ($no === $lineSpec 
						|| is_callable($lineSpec) && $lineSpec($no))
						$line = null;
		});

		return array_filter($input);
	}
}