<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Types\Table;

class Col implements ColBindInterface
{
	public $cols = array();

	public function __construct($col=null, $othercol=null, $etc=null)
	{
		$this->cols = func_get_args();
	}

	public function bindToCol(Col $target)
	{
		$mirror = new \ReflectionClass(__CLASS__);
		return $mirror->newInstanceArgs(array_merge($this->cols, $target->cols));
	}

	public function describe()
	{
		$description = array(
			array('_:'.spl_object_hash($this), 'php:Class', 'phpClass:'.get_called_class()),
		);

		foreach ($this->cols as $col)
			$description[] = array('_:'.spl_object_hash($this), 'respectConversion:OperatesOn', 'respectConversionTableCol:'.$col);

		return $description;
	}

}