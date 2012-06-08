<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Operators\Table\Tr\Callback as TrCallback;
use Respect\Conversion\Operators\Common\Common\AbstractCallback;
use Respect\Conversion\Selectors\Table\ColSelectInterface;

class Dehydrate extends TrCallback implements ColSelectInterface
{	

	public $col = null;
	public $operationCallback = null;

	public function __construct($col, $operationCallback = null)
	{
		$this->col = $col;
		$this->operationCallback = $operationCallback;
	}

	public function transform($target)
	{
		$this->selector->lines = array();
		$cols = $this->selector->cols;
		$col = $this->col;
		$callback = $this->operationCallback;
		
		$this->callback = function($v) use ($cols, $col, $callback) {
			$dehydrated = array();

			$n = 0;
			foreach ($v as $key => $vCol) {
				if (($n === $col || $key == $col) && is_array($vCol)) {
					$nn = 0;
					$vCol = $callback ? $callback($vCol) : $vCol;
					foreach ($vCol as $cn => $cv) {
						$dehydrated[$cn] = $cv;
						$n++;
					}
				}
				$n++;
			}
			foreach ($v as $key => $vCol) {
				if (($n !== $col && $key != $col) || !is_array($vCol)) 
					$dehydrated[$key] = $vCol;
				$n++;
			}

			return $dehydrated;
		};

		return parent::transform($target);
	}

}