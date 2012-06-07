<?php

namespace Respect\Conversion\Operators\Table\Col;

use Respect\Conversion\Selectors\Table\Col;
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

	public function operateUsing(Table $type, Col $selector)
	{
		$this->type = $type;
		$this->selector = $selector;
		return $this;
	}

	public function describe()
	{
		return array_merge(
			array(
				array('_:'.spl_object_hash($this), 'php:Class', 'phpClass:'.get_called_class()),
				array('_:'.spl_object_hash($this), 'respectConversion:TypedBy', $this->type ? '_:'.spl_object_hash($this->type) : null),
				array('_:'.spl_object_hash($this), 'respectConversion:SelectedBy', $this->selector ? '_:'.spl_object_hash($this->selector) : null)
			),
			$this->describeCallback($this->callback) ?: array()
		);
	}


	public function describeCallback($callback)
	{
		if (is_string($this->callback))
			return array(
				array('_:'.spl_object_hash($this), 'respectConversion:OperatorCallback', $cbId = '_:'.uniqid()),
				array($cbId, 'php:Function', 'phpFunction:'.$this->callback),
			);
		elseif (is_array($this->callback) 
				&& isset($this->callback[0]) 
				&& isset($this->callback[1])
				&& is_object($this->callback[0]))
			return array(
				array('_:'.spl_object_hash($this), 'respectConversion:OperatorCallback', $cbId = '_:'.uniqid()),
				array($cbId, 'php:Method', $methodId = uniqid()),
				array($methodId, 'php:Class', 'phpClass:'.get_called_class($this->callback[0])),
				array($methodId, 'php:Object', '_:'.spl_object_hash($this->callback[0])),
				array($methodId, 'php:MethodName', 'string:'.$this->callback[1])
			);
		elseif ($this->callback instanceof \Closure)
			return array(
				array('_:'.spl_object_hash($this), 'respectConversion:OperatorCallback', $cbId = '_:'.uniqid()),
				array($cbId, 'php:Closure', '_:'.spl_object_hash($this->callback))
			);
	}

	public function transform($input)
	{
		$cols = $this->selector->cols ?: array_keys($input);
		$callback = $this->callback;

		array_walk($input, function(&$line, $lineNo) use ($cols, $callback) {
			$n = 0;
			foreach ($line as $key => $col) {
				if (in_array($n, $cols))
					$line[$key] = call_user_func($callback, $line[$key]);
				$n++;
			}
			$line = array_filter($line);
		});

		return $input;
	}
}