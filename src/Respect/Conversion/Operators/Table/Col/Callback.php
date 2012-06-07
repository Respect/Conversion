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
				if (in_array($n, $cols, true))
					$line[$key] = call_user_func($callback, $line[$key]);
				$n++;
			}
		});

		return $input;
	}
}