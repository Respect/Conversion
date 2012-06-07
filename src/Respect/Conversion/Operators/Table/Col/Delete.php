<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Delete extends AbstractOperator implements ColSelectInterface
{
	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);

		array_walk($input, function(&$line, $lineNo) use ($cols) {
			$n = 0;
			foreach ($line as $key => $col) {
				foreach ($cols as $colSpec)
					if ($colSpec === $n
						|| is_callable($colSpec) && $colSpec($n))
						$line[$key] = null;
				$n++;
			}
		});

		return $input;
	}
}