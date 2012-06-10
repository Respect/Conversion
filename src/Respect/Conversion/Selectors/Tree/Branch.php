<?php

namespace Respect\Conversion\Selectors\Tree;

use Respect\Conversion\Types\Tree;
use Respect\Conversion\Selectors\Common\AbstractSelector;

class Branch extends AbstractSelector implements BranchBindInterface
{
	public $branches = array();

	public function __construct($branch = null, $otherbranch = null, $etc = null)
	{
		$this->branches = func_get_args();
	}
	public function bindToTreeBranch(Branch $target)
	{
		
	}
}