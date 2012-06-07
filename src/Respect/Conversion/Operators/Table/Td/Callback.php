<?php

namespace Respect\Conversion\Operators\Table\Td;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\TdSelectInterface;

class Callback extends AbstractCallback implements TdSelectInterface
{	
	public function transform($input)
	{
		$tds = $this->selector->tds;
		$callback = $this->callback;

		array_walk($input, function(&$line, $lineNo) use ($tds, $callback) {
			$n = 0;
			foreach ($line as $key => &$col) {
				foreach ($tds as $tdSpec)
					if (array($lineNo, $n) === $tdSpec      //specific cell
					    || array($lineNo, null) === $tdSpec //all cells from row
					    || array(null, $n) === $tdSpec      //all cells from column
					    || array(null, null) === $tdSpec   //all cells
					    || (is_callable($tdSpec) && $tdSpec($lineNo, $n)))
						$col = call_user_func($callback, $col);
				$n++;
			}
		});

		return $input;
	}
}