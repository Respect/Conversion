<?php

namespace Respect\Conversion\Operators\Common\Common;

class Sequence extends AbstractOperator
{
	public $operators;

	public function __construct($operator = null, $otherOperator = null, $etc = null)
	{
		$this->operators = func_get_args();
	}

	public function transform($input)
	{
		foreach ($this->operators as $o)
			$input = $o->transform($input);

		return $input;
	}
}