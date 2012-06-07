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
			foreach ($line as $key => $col) {
				if (in_array(array($lineNo, $n), $tds, true)
				    || in_array(array(null, $n), $tds, true)
				    || in_array(array($lineNo, null), $tds, true)
				    || in_array(array(null, null), $tds, true))
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