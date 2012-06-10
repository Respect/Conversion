<?php

namespace Respect\Conversion\Selectors\Collection;

use Respect\Conversion\Types\Collection;
use Respect\Conversion\Selectors\Common\AbstractSelector;

class Item extends AbstractSelector implements ItemBindInterface
{
	public $items = array();

	public function __construct($item=null, $otheritem=null, $etc=null)
	{
		$this->items = func_get_args();
	}

	public function bindToCollectionItem(Item $target)
	{
		$mirror = new \ReflectionClass(__CLASS__);
		return $mirror->newInstanceArgs(array_merge($this->items, $target->items));
	}
}