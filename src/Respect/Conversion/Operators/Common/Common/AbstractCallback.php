<?php

namespace Respect\Conversion\Operators\Common\Common;

abstract class AbstractCallback extends AbstractOperator
{
	public $type;
	public $selector;

	public function __construct($callback)
	{
		$this->callback = $callback;
	}

}