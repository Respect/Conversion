<?php

namespace Respect\Conversion\Operators\Table\Multi;

use Respect\Conversion\Selectors\Common\Multi;
use Respect\Conversion\Types\Table;

class Callback
{
	public $type;
	public $selector;
	public $callback;

	public function __construct($callback)
	{
		$this->callback = $callback;
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
				$input = static::operatorInstanceFromSelector($s, array($this->callback))
							   ->operateUsing($this->table, $s)
							   ->transform($input);
		return $input;
	}

	protected static function operatorInstanceFromSelector($selector, array $arguments = array())
	{
		$selectorClass = get_class($selector);
		$selectorClass = str_replace('\\Selectors\\', '\\Operators\\', $selectorClass);
		$operatorClass = $selectorClass.'\\Callback';
		$mirror = new \ReflectionClass($operatorClass);
		return $mirror->newInstanceArgs($arguments);
	}
}