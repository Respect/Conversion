<?php

namespace Respect\Conversion\Operators\Tree\Branch;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Tree\BranchSelectInterface;

class Callback extends AbstractCallback implements BranchSelectInterface
{
	public function transform($target)
	{
		$callback = $this->callback;

		return $this->applyCallbackOnBranches($callback, $target);
	}

	public function applyCallbackOnBranches($callback, $target)
	{
		$self = $this;
		$branches = $this->selector->branches;

		array_walk(&$target, function(&$v) use ($callback, $self, $branches) {
			if (!is_scalar($v))
				$v = $self->applyCallbackOnBranches($callback, $v);
			if (!is_scalar($v))
				if ($branches) {
					foreach ($branches as $branch)
						if (is_callable($branch) && !$cbResult = $branch($v));
						elseif (is_callable($branch) && $cbResult || $branch == $v)
							$v = $callback($v);
				} else {
					$v = $callback($v);
				}
		});
		return $target;
	}
}