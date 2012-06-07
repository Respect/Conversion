<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Common\Common\AbstractOperator;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Name extends AbstractOperator implements ColSelectInterface
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);
		$name = $this->name;

		array_walk($input, function(&$line, $lineNo) use ($cols, $name) {
			$newLine = array();
			$n = 0;
			foreach ($line as $key => $col) {
				foreach ($cols as $colSpec)
					if ($colSpec === $n
						|| is_callable($colSpec) && $colSpec($n))
						$newLine[$name] = $col;
					else
						$newline[$key] = $col;
				$n++;
			}
			$line = $newLine;
		});

		return $input;
	}
}