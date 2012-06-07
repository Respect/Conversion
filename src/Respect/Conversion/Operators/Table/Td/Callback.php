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
				if (in_array(array($lineNo, $n), $tds)      //specific cell
				    || in_array(array($lineNo, null), $tds) //all cells from row
				    || in_array(array(null, $n), $tds)      //all cells from column
				    || in_array(array(null, null), $tds))   //all cells
					$col = call_user_func($callback, $col);
				$n++;
			}
		});

		return $input;
	}
}