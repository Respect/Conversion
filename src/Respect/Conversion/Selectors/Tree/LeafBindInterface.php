<?php

namespace Respect\Conversion\Selectors\Tree;

interface LeafBindInterface
{
	public function bindToTreeLeaf(Leaf $selector);
}