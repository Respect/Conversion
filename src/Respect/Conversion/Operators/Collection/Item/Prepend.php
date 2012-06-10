<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Prepend extends Callback implements ItemSelectInterface
{
	public $string;

	public function __construct($string)
	{
		$this->string = $string;
		$this->callback = function($v) use ($string) {
			return $string.$v;
		};
	}

}