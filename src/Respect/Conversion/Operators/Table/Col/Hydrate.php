<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Table\Tr\Callback as TrCallback;
use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Hydrate extends TrCallback implements ColSelectInterface
{	
	public $name;
	public $operationCallback;

	public function __construct($name, $callback=null)
	{
		$this->name = $name;
		$this->operationCallback = $callback;
	}

	public function transform($target)
	{
		$name = $this->name;
		$this->selector->lines = array();
		$cols = $this->selector->cols;
		$callback = $this->operationCallback;
		
		$this->callback = function($v) use ($name, $cols, $callback) {
			$hydrated = array();

			$n = 0;
			foreach ($v as $key => $col) {
				if (in_array($key, $cols, true) || in_array($n, $cols, true)) {
					$hydrated[$key] = $v[$key];
					unset($v[$key]);					
				}
				$n++;
			}

			$v[$name] = $callback ? $callback($hydrated) : $hydrated;
			return $v;
		};

		return parent::transform($target);
	}

}