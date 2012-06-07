<?php

namespace Respect\Conversion\Operators\Table\Multi;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Common\MultiSelectInterface;

class Callback extends AbstractCallback implements MultiSelectInterface
{
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