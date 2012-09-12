<?php

namespace Respect\Conversion\Operators\Tree\Leaf;

use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Tree\LeafSelectInterface;

class Callback extends AbstractCallback implements LeafSelectInterface
{
	public function transform($target)
	{
		$callback = $this->callback;
		$leaves = $this->selector->leaves;

		array_walk_recursive($target, function(&$v) use ($callback, $leaves) {
			if (is_scalar($v))
				if ($leaves) {
					foreach ($leaves as $leave)
						if (is_callable($leave) && !$cbResult = $leave($v));
						elseif ((is_callable($leave) && $cbResult) || $leave == $v)
							$v = $callback($v);
				} else {
					$v = $callback($v);
				}
		});

		return $target;
	}
}