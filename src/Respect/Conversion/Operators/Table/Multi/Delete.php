<?php

namespace Respect\Conversion\Operators\Table\Multi;

use Respect\Conversion\Selectors\Common\Multi;
use Respect\Conversion\Types\Table;

class Delete
{
	public $type;
	public $multi;

	public function __construct()
	{
	}

	public function operateUsing(Table $type, Multi $selector)
	{
		$this->table = $type;
		$this->selector = $selector;
		return $this;
	}

	public function transform($input)
	{
		foreach ($this->selector->selectors as $s)
			$input = $s->buildOperator('Delete')
					   ->operateUsing($this->table, $s)
					   ->transform($input);

		return $input;
	}

}