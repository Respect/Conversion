<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Selectors\Table\Col;
use Respect\Conversion\Types\Table;

class Name
{
	public $type;
	public $selector;
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function operateUsing(Table $type, Col $selector)
	{
		$this->table = $type;
		$this->selector = $selector;
		return $this;
	}

	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);
		$name = $this->name;

		array_walk($input, function(&$line, $lineNo) use ($cols, $name) {
			$newLine = array();
			$n = 0;
			foreach ($line as $key => $col) {
				if (in_array($n, $cols))
					$newLine[$name] = $col;
				else
					$newLine[$key] = $col;
				$n++;
			}
			$line = array_filter($newLine);
		});

		return $input;
	}
}