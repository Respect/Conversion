<?php

namespace Respect\Conversion\Selectors\Tree;

interface BranchBindInterface
{
	public function bindToTreeBranch(Branch $selector);
}