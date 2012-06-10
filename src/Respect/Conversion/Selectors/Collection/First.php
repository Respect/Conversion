<?php

namespace Respect\Conversion\Selectors\Collection;

use Respect\Conversion\Types\Collection;
use Respect\Conversion\Selectors\Common\AbstractSelector;

class First extends AbstractSelector implements FirstBindInterface
{
	public $items = array();

	public function bindToCollectionFirst(First $target)
	{
		
	}
}