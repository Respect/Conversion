<?php

namespace Respect\Conversion\Operators\Table\Td;

use Respect\Conversion\Selectors\Table\Td;
use Respect\Conversion\Types\Table;

class Callback
{
	public $type;
	public $selector;
	public $callback;

	public function __construct($callback)
	{
		$this->callback = $callback;
	}

	public function operateUsing(Table $type, Td $selector)
	{
		$this->table = $type;
		$this->selector = $selector;
		return $this;
	}
	
	public function transform($input)
	{
		$tds = $this->selector->tds;
		$callback = $this->callback;

		array_walk($input, function(&$line, $lineNo) use ($tds, $callback) {
			$n = 0;
			foreach ($line as $key => &$col) {
				if (in_array(array($lineNo, $n), $tds)      //specific cell
				    || in_array(array($lineNo, null), $tds) //all cells from row
				    || in_array(array(null, $n), $tds)      //all cells from column
				    || in_array(array(null, null), $tds))   //all cells
					$col = call_user_func($callback, $col);
				$n++;
			}
			$line = array_filter($line);
		});

		return array_filter($input);
	}
}