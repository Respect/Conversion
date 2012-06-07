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

			if (empty($lines))
				$line = call_user_func($callback, $line);
			else
				foreach ($lines as $lineSpec)
					if ($no === $lineSpec 
						|| is_callable($lineSpec) && $lineSpec($no))
						$line = call_user_func($callback, $line);
		});

		return $input;
	}
}