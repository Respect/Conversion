<?php

namespace Respect\Conversion\Operators\Table\Tr;

use Respect\Conversion\Selectors\Table\Tr;
use Respect\Conversion\Types\Table;

class Delete
{
	public $type;
	public $selector;

	public function __construct()
	{
		
	}

	public function operateUsing(Table $type, Tr $selector)
	{
		$this->table = $type;
		$this->selector = $selector;
		return $this;
	}
	
	public function transform($input)
	{
		$lines = $this->selector->lines;

		array_walk($input, function(&$line, $no) use ($lines) {
			if (in_array($no, $lines))
				$line = null;
		});

		return array_filter($input);
	}
}