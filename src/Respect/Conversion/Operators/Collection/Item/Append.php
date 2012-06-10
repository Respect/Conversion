<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Append extends Callback implements ItemSelectInterface
{
	public $string;

	public function __construct($string)
	{
		$this->string = $string;
		$this->callback = function($v) use ($string) {
			return $v.$string;
		};
	}

}