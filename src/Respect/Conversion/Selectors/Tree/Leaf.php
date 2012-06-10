<?php

namespace Respect\Conversion\Selectors\Tree;

use Respect\Conversion\Types\Tree;
use Respect\Conversion\Selectors\Common\AbstractSelector;

class Leaf extends AbstractSelector implements LeafBindInterface
{
	public $leaves = array();

	public function __construct($leaf = null, $otherLeaf = null, $etc = null)
	{
		$this->leaves = func_get_args();
	}

	public function bindToTreeLeaf(Leaf $target)
	{
		
	}
}