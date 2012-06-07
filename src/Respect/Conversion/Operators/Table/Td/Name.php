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
					if (array($lineNo, $n) === $tdSpec      //specific cell
					    || array($lineNo, null) === $tdSpec //all cells from row
					    || array(null, $n) === $tdSpec      //all cells from column
					    || array(null, null) === $tdSpec   //all cells
					    || (is_callable($tdSpec) && $tdSpec($lineNo, $n)))
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