<?php

namespace Respect\Conversion;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
	public function test_type()
	{
		$table = array(
			array('id' => 0, 'name' => 'Alexandre 0', 'internal_code' => 9345343846),
			array('id' => 1, 'name' => 'Alexandre', 'internal_code' => 9345343846),
			array('id' => 2, 'name' => 'Fulano', 'internal_code' => 933546),
			array('id' => 3, 'name' => 'John Doe', 'internal_code' => 9334546),
			array('id' => 4, 'name' => 'John Doe 2', 'internal_code' => 9334546),
			array('id' => 5, 'name' => 'John Doe 3', 'internal_code' => 9334546)
		);
		$c = Converter::table()
					  ->tr()
					  ->callback(function($line) {
						  	$line['name'] .= ' do Nascimento';
						  	return $line;
					   })->col(1)
					  ->callback('strrev')
					  ->describe($table);

		print_r($c);

	}
}