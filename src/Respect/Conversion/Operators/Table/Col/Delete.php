<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Selectors\Table\Col;
use Respect\Conversion\Types\Table;

class Delete
{
	public $type;
	public $selector;

	public function __construct()
	{
		
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

		array_walk($input, function(&$line, $lineNo) use ($cols) {
			$n = 0;
			foreach ($line as $key => $col) {
				if (in_array($n, $cols))
					$line[$key] = null;
				$n++;
			}
			$line = array_filter($line);
		});

		return $input;
	}
}