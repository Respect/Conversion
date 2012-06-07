<?php

namespace Respect\Conversion\Types;

use Respect\Conversion\Converter;

class Table
{
	public function __construct()
	{
		
	}

	public function describe()
	{
		return array(
			array('_:'.spl_object_hash($this), 'php:Class', 'phpClass:'.get_called_class())
		);
	}
}
