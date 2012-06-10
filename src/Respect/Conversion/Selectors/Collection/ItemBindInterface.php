<?php

namespace Respect\Conversion\Selectors\Collection;

interface ItemBindInterface
{
	public function bindToCollectionItem(Item $selector);
}