<?php

namespace Respect\Conversion\Operators\Collection\Item;

use Respect\Conversion\Selectors\Collection\ItemSelectInterface;

class Delete extends Callback implements ItemSelectInterface
{
	public function __construct()
	{
		$this->callback = function($v) {
			return null;
		};
	}

	public function transform($target)
	{
		return array_filter(parent::transform($target));
	}

}