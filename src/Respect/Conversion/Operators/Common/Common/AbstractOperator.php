<?php

namespace Respect\Conversion\Operators\Common\Common;

abstract class AbstractOperator
{
	public $type;
	public $selector;

	public function __construct()
	{
		// ReflectionClass::newInstanceArgs requires a declared constructor
	}

	public function operateUsing($type, $selector)
	{
		$this->type = $type;
		$this->selector = $selector;
		return $this;
	}

	abstract public function transform($input);
}