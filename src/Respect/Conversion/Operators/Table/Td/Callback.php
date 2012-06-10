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
					if (is_callable($tdSpec) && !($cbResult = $tdSpec($lineNo, $n)));
					elseif (is_callable($tdSpec) && $cbResult     // CALLABLE -----------
					    || array(null, null)        === $tdSpec   // ALL CELLS ----------
					    || (is_null($tdSpec[1]) 
					    	&& array($lineNo, null) === $tdSpec)  //all cells from row
					    || (is_numeric($tdSpec[1])                // NUMERIC ------------
							&& array($lineNo, $n)   === $tdSpec)  //specific cell
					    || (is_null($tdSpec[0]) 
					    	&& array(null, $n)      === $tdSpec)  //all cells from column
					    									      
					    || (is_string($tdSpec[1])                 // STRING -------------
					    	&& array($lineNo, $key) === $tdSpec)  //specific cells
					    || (is_string($tdSpec[1])
					    	&& array(null, $key) === $tdSpec))    //all cells from row
						$col = call_user_func($callback, $col);
				$n++;
			}
		});

		return $input;
	}
}