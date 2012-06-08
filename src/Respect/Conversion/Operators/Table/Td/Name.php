<?php

namespace Respect\Conversion\Operators\Table\Td;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\TdSelectInterface;

class Name extends AbstractOperator implements TdSelectInterface
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function transform($input)
	{
		$tds = $this->selector->tds ?: null;
		$name = $this->name;

		array_walk($input, function(&$line, $lineNo) use ($tds, $name) {
			$newLine = array();
			$n = 0;
			foreach ($line as $key => &$col) {
				foreach ($tds as $tdSpec)
					if (is_callable($tdSpec) && !($cbResult = $tdSpec($lineNo, $n)));
					elseif (is_callable($tdSpec) && $cbResult     // CALLABLE -----------
					    || array(null, null)        === $tdSpec   // ALL CELLS ----------
					    || (is_numeric($tdSpec[1])                // NUMERIC ------------
							&& array($lineNo, $n)   === $tdSpec)  //specific cell
					    || (is_null($tdSpec[1]) 
					    	&& array($lineNo, null) === $tdSpec)  //all cells from row
					    || (is_null($tdSpec[0]) 
					    	&& array(null, $n)      === $tdSpec)  //all cells from column
					    									      
					    || (is_string($tdSpec[1])                 // STRING -------------
					    	&& array($lineNo, $key) === $tdSpec)) //all cells from row
						$newLine[$name] = $col;
					else
						$newLine[$key] = $col; 
				$n++;
			}
			$line = $newLine;
		});

		return $input;
	}
}