<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Up extends AbstractOperator implements ColSelectInterface
{
	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);
		$output = array();

		array_walk($input, function(&$line, $lineNo) use ($cols, &$output) {
			$n = 0;
			$output[$lineNo] = array();
			foreach ($line as $key => $col) {
				foreach ($cols as $colSpec)
					if ((is_numeric($colSpec) && $colSpec === $n)
						|| (is_string($colSpec) && $colSpec == $key)
						|| (is_callable($colSpec) && $colSpec($n)))
							$output[$lineNo][$key] = $col;
				$n++;
			}
			$output[$lineNo] = count($output[$lineNo]) == 1 ? end($output[$lineNo]) : $output[$lineNo];
		});

		return count($output) == 1 ? current($output) : $output;
	}
}