<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\TrSelectInterface;

class Callback extends AbstractCallback implements TrSelectInterface
{
	public function transform($input)
	{
		$lines = $this->selector->lines;
		$callback = $this->callback;

		array_walk($input, function(&$line, $no) use ($lines, $callback) {
			if (in_array($no, $lines, true) || empty($lines))
				$line = call_user_func($callback, $line);
		});

		return $input;
	}
}