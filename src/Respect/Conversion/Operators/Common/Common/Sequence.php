<?php

namespace Respect\Conversion\Operators\Common\Common;

class Sequence
{
	public $type;
	public $selector;
	public $operators;

	public function __construct($operator = null, $otherOperator = null, $etc = null)
	{
		$this->operators = func_get_args();
	}

	public function operateUsing($type, $selector)
	{
		$this->type = $type;
		$this->selector = $selector;
		return $this;
	}

	public function transform($input)
	{
		foreach ($this->operators as $o)
			$input = $o->transform($input);

		return $input;
	}

	public function describe()
	{
		$description = array(
			array('_:'.spl_object_hash($this), 'php:Class', 'phpClass:'.get_called_class()),
			array('_:'.spl_object_hash($this), 'respectConversion:SelectedBy', $this->type ? '_:'.spl_object_hash($this->type) : null),
			array('_:'.spl_object_hash($this), 'respectConversion:TypedBy', $this->selector ? '_:'.spl_object_hash($this->selector) : null)
		);

		foreach ($this->operators as $o) {
			$description[] = array('_:'.spl_object_hash($this), 'respectConversion:AggregatesOperator', '_:'.spl_object_hash($o));
			$description = array_merge($description, $o->describe());
		}

		return $description;
	}
}