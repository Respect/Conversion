<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Table\Tr\Callback as TrCallback;
use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Hydrate extends TrCallback implements ColSelectInterface
{	
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function transform($target)
	{
		$name = $this->name;
		$this->selector->lines = array();
		$cols = $this->selector->cols;
		
		$this->callback = function($v) use ($name, $cols) {
			$hydrated = array();

			$n = 0;
			foreach ($v as $key => $col) {
				if (in_array($key, $cols, true) || in_array($n, $cols, true)) {
					$hydrated[$key] = $v[$key];
					unset($v[$key]);					
				}
				$n++;
			}

			$v[$name] = $hydrated;
			return $v;
		};

		return parent::transform($target);
	}

}