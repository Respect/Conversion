<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Callback extends AbstractCallback implements ColSelectInterface
{
	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);
		$callback = $this->callback;

		array_walk($input, function(&$line, $lineNo) use ($cols, $callback) {
			$n = 0;
			foreach ($line as $key => $col) {
				foreach ($cols as $colSpec)
					if ((is_numeric($colSpec) && $colSpec === $n)
						|| (is_string($colSpec) && $colSpec == $key)
						|| (is_callable($colSpec) && $colSpec($n)))
						$line[$key] = call_user_func($callback, $line[$key]);
				$n++;
			}
		});

		return $input;
	}
}