<?php

namespace Respect\Conversion;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->input = array(
			array('id' => 0, 'name' => 'Alexandre 0', 'internal_code' => 9345343846),
			array('id' => 1, 'name' => 'Alexandre', 'internal_code' => 9345343846),
			array('id' => 2, 'name' => 'Fulano', 'internal_code' => 933546),
			array('id' => 3, 'name' => 'John Doe', 'internal_code' => 9334546),
			array('id' => 4, 'name' => 'John Doe 2', 'internal_code' => 9334546),
			array('id' => 5, 'name' => 'John Doe 3', 'internal_code' => 9334546)
		);
	}

	public function testAbstractOperatorUsesTypesAndSelectorsProperly()
	{
		$operator = new Operators\Table\Td\Callback('strrev');
		$operator->operateUsing(new Types\Table, new Selectors\Table\Td(array(1,1)));

		$result = $operator->transform($this->input);
		$this->assertEquals('erdnaxelA', $result[1]['name']);

		return $result;
	}

	public function testSequenceAppliesToItsChildrenInCorrectSequence()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Td(array(1,1));
		$op1 = new Operators\Table\Td\Callback('strrev');
		$op2 = new Operators\Table\Td\Callback('ucfirst');
		$op1->operateUsing($type, $selector);
		$op2->operateUsing($type, $selector);
		$operator = new Operators\Common\Common\Sequence($op1, $op2);
		$operator2 = new Operators\Common\Common\Sequence($op2, $op1);

		$result = array();
		$result[] = $operator->transform($this->input);
		$this->assertEquals('ErdnaxelA', $result[0][1]['name']);
		$result[] = $operator2->transform($this->input);
		$this->assertEquals('erdnaxelA', $result[1][1]['name']);

		return $result;
	}
	public function testTableMultiDeleteAppliesToItsChildrenInCorrectSequence()
	{
		$type = new Types\Table;
		$selector1 = new Selectors\Table\Col(1);
		$op1 = new Operators\Table\Col\Delete;
		$op1->operateUsing($type, $selector1);
		$selector2 = new Selectors\Table\Tr(1);
		$op2 = new Operators\Table\Tr\Delete;
		$op2->operateUsing($type, $selector2);
		$operator = new Operators\Common\Common\Sequence($op1, $op2);

		$result = $operator->transform($this->input);
		$this->assertEquals(count($this->input)-1, count($result));

		return $result;
	}

	protected function abstractTableColOperatorTest($name, array $colParams, array $operatorParams, $verifier)
	{
		$type = new Types\Table;
		$selectorClass = new \ReflectionClass('Respect\Conversion\Selectors\Table\Col');
		$selector = $selectorClass->newInstanceArgs($colParams);
		$operatorClass = new \ReflectionClass('Respect\Conversion\Operators\Table\Col\\'.$name);
		$operator = $operatorClass->newInstanceArgs($operatorParams);
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);

		foreach ($result as $tr => $line) {
			$n = 0;
			foreach ($line as $td => $cell) {
				if (!empty($colParams))
					if (in_array($n, $colParams, true) || in_array($td, $colParams, true))
						$this->assertEquals(call_user_func($verifier, $this->input[$tr][$td], $td), $result[$tr][$td]);
					else 
						$this->assertEquals($this->input[$tr][$td], $result[$tr][$td], $td);
				else
					$this->assertEquals(call_user_func($verifier, $this->input[$tr][$td], $td), $result[$tr][$td]);
				$n++;
			}
		}

		return $result;
	}
	protected function abstractTableTrOperatorTest($name, array $rowParams, array $operatorParams, $verifier)
	{
		$type = new Types\Table;
		$selectorClass = new \ReflectionClass('Respect\Conversion\Selectors\Table\Tr');
		$selector = $selectorClass->newInstanceArgs($rowParams);
		$operatorClass = new \ReflectionClass('Respect\Conversion\Operators\Table\Tr\\'.$name);
		$operator = $operatorClass->newInstanceArgs($operatorParams);
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);

		$n = 0;
		foreach ($result as $tr => $line) {
			if (!empty($rowParams))
				if (in_array($n, $rowParams))
					$this->assertEquals(call_user_func($verifier, $this->input[$tr], $tr, $n), $result[$tr]);
				else 
					$this->assertEquals($this->input[$tr], $result[$tr], $tr, $n);
			else
				$this->assertEquals(call_user_func($verifier, $this->input[$tr], $tr, $n), $result[$tr]);
			$n++;
		}

		return array_filter($result);
	}


	public function testTableColDeleteAssocAppliesOnlyToSingleColumn()
	{
		return $this->abstractTableColOperatorTest('Delete', array("name"), array(), function($v) {
			return null;
		});
	}

	public function testTableColDeleteAssocAppliesOnlyToSelectedColumns()
	{
		return $this->abstractTableColOperatorTest('Delete', array("internal_code", "name"), array(), function($v) {
			return null;
		});
	}

	public function testTableColDeleteAppliesOnlyToSingleColumn()
	{
		return $this->abstractTableColOperatorTest('Delete', array(1), array(), function($v) {
			return null;
		});
	}

	public function testTableColDeleteAppliesToAllColumns()
	{
		return $this->abstractTableColOperatorTest('Delete', array(), array(), function($v) {
			return null;
		});
	}

	public function testTableColDeleteAppliesOnlyToSelectedColumns()
	{
		return $this->abstractTableColOperatorTest('Delete', array(0,2,3), array(), function($v) {
			return null;
		});
	}

	public function testTableTrCallbackAppliesOnlyToSingleRow()
	{
		$input = $this->input;
		return $this->abstractTableTrOperatorTest('Callback', array(1), array('implode'), function($v, $k, $n) use($input) {
			return $n == 1 ? implode($v) : $v;
		});
	}

	public function testTableTrCallbackAppliesToAllRows()
	{
		$input = $this->input;
		return $this->abstractTableTrOperatorTest('Callback', array(), array('implode'), function($v, $k, $n) use($input) {
			return implode($v);
		});
	}

	public function testTableTrCallbackAppliesOnlyToSelectedRows()
	{
		$input = $this->input;
		return $this->abstractTableTrOperatorTest('Callback', array(0,2,3), array('implode'), function($v, $k, $n) use($input) {
			return in_array($n, (array(0,2,3))) ? implode($v) : $v;
		});
	}

	public function testTableTrDeleteAppliesOnlyToSingleRow()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Tr(0);
		$operator = new Operators\Table\Tr\Delete();
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);
		$this->assertEquals(count($this->input)-1, count($result));
	}

	public function testTableTrDeleteAppliesToAllRows()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Tr();
		$operator = new Operators\Table\Tr\Delete();
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);
		$this->assertEquals(0, count($result));
	}

	public function testTableTrDeleteAppliesOnlyToSelectedRows()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Tr(0,2,3);
		$operator = new Operators\Table\Tr\Delete();
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);
		$this->assertEquals(count($this->input)-3, count($result));
	}

	public function testTableColNameAppliesToColumns()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Col(0);
		$operator = new Operators\Table\Col\Name("Código");
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);

		foreach ($result as $tr => $line) {
			$n = 0;
			foreach ($line as $td => $cell) {
				if ($n === 0)
					$this->assertEquals("Código", $td);
				else
					$this->assertEquals(array_search($cell, $this->input[$tr], true), $td);

				$n++;
			}
		}
	}

	public function testTableColNameAssocAppliesToColumns()
	{
		$type = new Types\Table;
		$selector = new Selectors\Table\Col("name");
		$operator = new Operators\Table\Col\Name("Full Name");
		$operator->operateUsing($type, $selector);

		$result = $operator->transform($this->input);

		foreach ($result as $tr => $line) {
			$n = 0;
			if (empty($line))
				$this->fail('Empty line');
			foreach ($line as $td => $cell) {
				if ($n === 1)
					$this->assertEquals("Full Name", $td);
				else
					$this->assertEquals(array_search($cell, $this->input[$tr], true), $td);

				$n++;
			}
		}
	}

	public function testTableTdCallbackAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,1))
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
	}
	public function testTableTdAssocCallbackAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,"name"))
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
	}

	public function testTableColCallbackAppliesToSpecificColsUsingClosureSpec() 
	{
		$result = Converter::table()
		                       ->td(function($n) { return $n === 1; })
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
	}
	public function testTableTrCallbackAppliesToSpecificRowsUsingClosureSpec() 
	{
		$result = Converter::table()
		                       ->tr(function($n) { return $n === 1; })
		                           ->callback('implode')
		                   ->transform($this->input);

		$this->assertEquals('1Alexandre9345343846', $result[1]);
	}
	public function testTableTdCallbackAppliesToSpecificCellsUsingClosureSpec() 
	{
		$result = Converter::table()
		                       ->td(function($tr, $col) { return $tr === 1 && $col === 1; })
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
	}


	public function testTableTdDeleteAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,1))
		                           ->delete()
		                   ->transform($this->input);

		$this->assertEquals(null, $result[1]["name"]);
	}

	public function testTableTdNameAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,1))
		                           ->name("something")
		                   ->transform($this->input);

		$this->assertEquals('Alexandre', $result[1]["something"]);
	}
	public function testTableTrNameAppliesToSpecificRows() 
	{
		$result = Converter::table()
		                       ->tr(1)
		                           ->name("something")
		                   ->transform($this->input);

		$this->assertEquals('Alexandre', $result["something"]["name"]);
	}
	public function testTableColNameAppliesToSpecificCols() 
	{
		$result = Converter::table()
		                       ->col(1)
		                           ->name("something")
		                   ->transform($this->input);	

		$this->assertEquals('Alexandre', $result[1]["something"]);
		$this->assertEquals('Alexandre 0', $result[0]["something"]);
	}
	public function testTableColPrependAppliesToSpecificCols() 
	{
		$result = Converter::table()
		                       ->td(array(null,1))
		                           ->prepend("something")
		                   ->transform($this->input);	

		$this->assertEquals('somethingAlexandre', $result[1]["name"]);
		$this->assertEquals('somethingAlexandre 0', $result[0]["name"]);
	}

	public function testTableColPrependAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,1))
		                           ->prepend("something")
		                   ->transform($this->input);	

		$this->assertEquals('somethingAlexandre', $result[1]["name"]);
		$this->assertEquals(1, $result[1]["id"]);
	}
	public function testTableColAppendAppliesToSpecificCols() 
	{
		$result = Converter::table()
		                       ->td(array(null,1))
		                           ->append("something")
		                   ->transform($this->input);	

		$this->assertEquals('Alexandresomething', $result[1]["name"]);
		$this->assertEquals('Alexandre 0something', $result[0]["name"]);
	}

	public function testTableColAppendAppliesToSpecificCells() 
	{
		$result = Converter::table()
		                       ->td(array(1,1))
		                           ->append("something")
		                   ->transform($this->input);	

		$this->assertEquals('Alexandresomething', $result[1]["name"]);
		$this->assertEquals(1, $result[1]["id"]);
	}

	public function testTableColDuplicateAppliesToSpecificCols() 
	{
		$result = Converter::table()
		                       ->col(1)
		                           ->duplicate("something")
		                   ->transform($this->input);	

		$this->assertEquals('Alexandre', $result[1]["name"]);
		$this->assertEquals('Alexandre', $result[1]["something"]);
	}


	public function testTableColDuplicateAppliesToSpecificColsCallback() 
	{
		$result = Converter::table()
		                       ->col(1)
		                           ->duplicate("something", 'strrev')
		                   ->transform($this->input);	

		$this->assertEquals('Alexandre', $result[1]["name"]);
		$this->assertEquals('erdnaxelA', $result[1]["something"]);
	}

	public function testTableColHydrateAppliesToSpecificCols() 
	{
		$result = Converter::table()
		                       ->col(0,1)
		                           ->hydrate("something")
		                   ->transform($this->input);	

		$this->assertSame(array('id'=>1, 'name'=>'Alexandre'), $result[1]["something"]);
		$this->assertSame(array('id'=>0, 'name'=>'Alexandre 0'), $result[0]["something"]);

		$result = Converter::table()
						       ->col("id", 1)
						           ->dehydrate(0)
						   ->transform($result);


		$this->assertSame($this->input, $result);
	}


	public function testCollectionItemAppendAppliesToSpecificItemsCallback() 
	{
		$result = Converter::collection()
		                       ->item(function($v, $k) {return $v == 'bar' && $k == 1;})
		                           ->append("something")
		                   ->transform(array('foo', 'bar', 'baz'));

		$this->assertEquals('barsomething', $result[1]);
	}

	public function testCollectionItemAppendAppliesToSpecificItems() 
	{
		$result = Converter::collection()
		                       ->item(1)
		                           ->append("something")
		                   ->transform(array('foo', 'bar', 'baz'));

		$this->assertEquals('barsomething', $result[1]);
	}

	public function testCollectionItemNameAppliesToSpecificItems() 
	{
		$result = Converter::collection()
		                       ->item(1)
		                           ->name("something")
			                   ->transform(array('foo', 'bar', 'baz'));

		$this->assertEquals(array('foo', 'something'=>'bar', 2 => 'baz'), $result);
	}

	public function testCollectionItemExtractAppliesToSpecificItems() 
	{
		$result = Converter::collection()
		                       ->item(1)
		                           ->extract()
			                   ->transform(array('foo', array('lorem' => 'bar', 'ipsum'), 'baz'));

		$this->assertEquals(array('foo', 'ipsum', 'lorem' => 'bar', 'baz'), $result);
	}
	public function testCollectionItemExtractAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->item("doo")
		                           ->extract()
			                   ->transform(array('foo', 'doo' => array('lorem' => 'bar', 'ipsum'), 'baz'));

		$this->assertEquals(array('foo', 'ipsum', 'lorem' => 'bar', 'baz'), $result);
	}

	public function testCollectionFirstExtractAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->extract()
			                   ->transform(array('doo' => array('lorem' => 'bar', 'ipsum'), 'baz', 'foo'));

		$this->assertEquals(array('lorem' => 'bar', 'ipsum', 'baz', 'foo'), $result);
	}
	public function testCollectionFirstAppendAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->append("gloo")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('foogloo', 'baz', 'bar'), $result);
	}
	public function testCollectionFirstPrependAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->prepend("gloo")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('gloofoo', 'baz', 'bar'), $result);
	}
	public function testCollectionFirstCallbackAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->callback("strrev")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('oof', 'baz', 'bar'), $result);
	}
	public function testCollectionFirstDeleteAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->delete()
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array(1 => 'baz', 2 => 'bar'), $result);
	}
	public function testCollectionFirstNameAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->name("something")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('something' => 'foo', 1 => 'baz', 2 => 'bar'), $result);
	}

	public function testCollectionLastExtractAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->extract()
			                   ->transform(array('baz', 'foo', 'doo' => array('lorem' => 'bar', 'ipsum')));

		$this->assertEquals(array('baz', 'foo', 'lorem' => 'bar', 'ipsum'), $result);
	}
	public function testCollectionLastAppendAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->append("gloo")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('foo', 'baz', 'bargloo'), $result);
	}
	public function testCollectionLastPrependAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->prepend("gloo")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('foo', 'baz', 'gloobar'), $result);
	}
	public function testCollectionLastCallbackAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->callback("strrev")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array('foo', 'baz', 'rab'), $result);
	}
	public function testCollectionLastDeleteAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->delete()
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array(0 => 'foo', 1 => 'baz'), $result);
	}
	public function testCollectionLastNameAppliesToSpecificItemsAssoc() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->name("something")
			                   ->transform(array('foo', 'baz', 'bar'));

		$this->assertEquals(array(0 => 'foo', 1 => 'baz', 'something' => 'bar'), $result);
	}
	public function testTreeLeafCallbackAppliesToLeaves() 
	{
		$result = Converter::tree()
		                       ->leaf()
		                           ->callback("strrev")
			                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
	}
	public function testTreeLeafCallbackAppliesToLeavesVerifier() 
	{
		$result = Converter::tree()
		                       ->leaf("Alexandre")
		                           ->callback("strrev")
			                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
		$this->assertEquals('Alexandre 0', $result[0]["name"]);
	}
	public function testTreeLeafCallbackAppliesToLeavesVerifierCallback() 
	{
		$result = Converter::tree()
		                       ->leaf(function($v){return $v == 'Alexandre';})
		                           ->callback("strrev")
			                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
		$this->assertEquals('Alexandre 0', $result[0]["name"]);
	}
	public function testTreeBranchCallbackAppliesToLeaves() 
	{
		$result = Converter::tree()
		                       ->branch()
		                           ->callback("implode")
			                   ->transform($this->input);
			                   
		$this->assertEquals('1Alexandre9345343846', $result[1]);
	}

	public function testTreeBranchCallbackAppliesToLeavesVerifier() 
	{
		$result = Converter::tree()
		                       ->branch(function($v) {
			                           return isset($v['name']) && $v['name'] == 'Alexandre';
		                            })->callback("implode")
			                   ->transform($this->input);

		$this->assertEquals('1Alexandre9345343846', $result[1]);
		$this->assertEquals(1, count(array_diff($result, $this->input)));
	}
	public function testTreeBranchCallbackAppliesToLeavesMulti() 
	{
		$this->input[1]["name"] = str_split($this->input[1]["name"]);
		$result = Converter::tree()
		                       ->branch()
		                           ->callback("implode")
			                   ->transform($this->input);
			                   
		$this->assertEquals('1Alexandre9345343846', $result[1]);
	}
	public function testCollectionFirstUp() 
	{
		$result = Converter::collection()
		                       ->first()
		                           ->up()
			                   ->transform($this->input);
			                   
		$this->assertEquals($this->input[0], $result);
	}
	public function testCollectionLastUp() 
	{
		$result = Converter::collection()
		                       ->last()
		                           ->up()
			                   ->transform($this->input);
			                   
		$this->assertEquals(end($this->input), $result);
	}
	public function testCollectionItemUp() 
	{
		$result = Converter::collection()
		                       ->item(2)
		                           ->up()
			                   ->transform($this->input);

		$this->assertEquals($this->input[2], $result);
	}
	public function testCollectionItemUpMultiple() 
	{
		$result = Converter::collection()
		                       ->item(2,3)
		                           ->up()
			                   ->transform($this->input);

		$this->assertEquals(array_slice($this->input,2,2), array_values($result));
	}
	public function testTableColUp() 
	{
		$result = Converter::table()
		                       ->col(1)
		                           ->up()
			                   ->transform($this->input);

	    $expected = array('Alexandre 0', 'Alexandre', 'Fulano', 'John Doe', 'John Doe 2', 'John Doe 3');
		$this->assertEquals($expected, $result);
	}

	public function testTableColUpMultiple() 
	{
		$result = Converter::table()
		                       ->col(1, 2)
		                           ->up()
			                   ->transform($this->input);

	    $expected = array (
		  0 => 
		  array (
		    'name' => 'Alexandre 0',
		    'internal_code' => 9345343846,
		  ),
		  1 => 
		  array (
		    'name' => 'Alexandre',
		    'internal_code' => 9345343846,
		  ),
		  2 => 
		  array (
		    'name' => 'Fulano',
		    'internal_code' => 933546,
		  ),
		  3 => 
		  array (
		    'name' => 'John Doe',
		    'internal_code' => 9334546,
		  ),
		  4 => 
		  array (
		    'name' => 'John Doe 2',
		    'internal_code' => 9334546,
		  ),
		  5 => 
		  array (
		    'name' => 'John Doe 3',
		    'internal_code' => 9334546,
		  ),
		);
		$this->assertEquals($expected, $result);
	}

	public function testTableColHydrateAppliesToSpecificColsWithCallback() 
	{
		$result = Converter::table()
		                       ->col(0,1)
		                           ->hydrate("something", $cb = function($v) {
		                           	return array_map('strrev', $v);
		                           })
		                   ->transform($this->input);	

		$this->assertSame(array('id'=>'1', 'name'=>'erdnaxelA'), $result[1]["something"]);
		$this->assertSame(array('id'=>'0', 'name'=>'0 erdnaxelA'), $result[0]["something"]);

		$result = Converter::table()
						       ->col("id", 1)
						           ->dehydrate(0, $cb)
						   ->transform($result);

		$this->assertEquals($this->input, $result);
	}

	public function testTableTdCallbackAppliesToCellsFromColumn() 
	{
		$result = Converter::table()
		                       ->td(array(null,1))
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('0 erdnaxelA', $result[0]["name"]);
		$this->assertEquals('erdnaxelA', $result[1]["name"]);
		$this->assertEquals('onaluF', $result[2]["name"]);
		$this->assertEquals('eoD nhoJ', $result[3]["name"]);
		$this->assertEquals('2 eoD nhoJ', $result[4]["name"]);
		$this->assertEquals('3 eoD nhoJ', $result[5]["name"]);
	}

	public function testTableTdCallbackAppliesToCellsFromRow() 
	{
		$result = Converter::table()
		                       ->td(array(1,null))
		                           ->callback('strrev')
		                   ->transform($this->input);

		$this->assertEquals('erdnaxelA', $result[1]["name"]);
		$this->assertEquals('6483435439', $result[1]["internal_code"]);
	}

	public function testTableTdCallbackAppliesToAllCells() 
	{
		$phpUnit = $this;
		array_walk_recursive(
			Converter::table()
			             ->td(array(null,null))
			                 ->callback(function(){return null;})
			         ->transform($this->input), 
			function ($v) use($phpUnit) {
				$phpUnit->assertEquals(null, $v);
			}
		);
	}

	public function testTableColBindsToTableColOnSpecificCells()
	{
		$selector1 = new Selectors\Table\Col(1,3);
		$selector2 = new Selectors\Table\Col(2,4);
		$selector = $selector1->bindToTableCol($selector2);
		$this->assertEquals(array(1,3,2,4), $selector->cols);
	}

	public function testTableColBindsToTableTrOnSpecificCells()
	{
		$selector1 = new Selectors\Table\Col(1,3);
		$selector2 = new Selectors\Table\Tr(2,4);
		$selector = $selector1->bindToTableTr($selector2);
		$this->assertEquals($expected = array(
			array(2,1),
			array(2,3),
			array(4,1),
			array(4,3)
		), $selector->tds);
		$selector = $selector2->bindToTableCol($selector1);
		$this->assertEquals($expected, $selector->tds);
	}

	public function testTableColBindsToTableTrOnCellsFromRow()
	{
		$selector1 = new Selectors\Table\Col(1,3);
		$selector2 = new Selectors\Table\Tr();
		$selector = $selector1->bindToTableTr($selector2);
		$this->assertEquals($expected = array(
			array(null,1),
			array(null,3)
		), $selector->tds);
		$selector = $selector2->bindToTableCol($selector1);
		$this->assertEquals($expected, $selector->tds);
	}

	public function testTableColBindsToTableTrOnCellsFromCol()
	{
		$selector1 = new Selectors\Table\Col(null);
		$selector2 = new Selectors\Table\Tr(2,4);
		$selector = $selector1->bindToTableTr($selector2);
		$this->assertEquals($expected = array(
			array(2,null),
			array(4,null)
		), $selector->tds);
		$selector = $selector2->bindToTableCol($selector1);
		$this->assertEquals($expected, $selector->tds);
	}
	public function testTableColBindsToTableTrOnAllCells()
	{
		$selector1 = new Selectors\Table\Col();
		$selector2 = new Selectors\Table\Tr();
		$selector = $selector1->bindToTableTr($selector2);
		$this->assertEquals($expected = array(), $selector->tds);
		$selector = $selector2->bindToTableCol($selector1);
		$this->assertEquals($expected, $selector->tds);
	}

	public function testTableTdBindsToTableTdInOrder()
	{
		$selector1 = new Selectors\Table\Td(array(0,2));
		$selector2 = new Selectors\Table\Td(array(3,4));
		$selector = $selector1->bindToTableTd($selector2);
		$this->assertEquals(array(
			array(0,2),
			array(3,4)
		), $selector->tds);
		$selector = $selector2->bindToTableTd($selector1);
		$this->assertEquals(array(
			array(3,4),
			array(0,2)
		), $selector->tds);
	}

	public function testConverterAppliesChainProperly()
	{
		$expected = $this->testAbstractOperatorUsesTypesAndSelectorsProperly();
		$result = Converter::table()
							   ->td(array(1,1))
							       ->callback('strrev')
						   ->transform($this->input);
		$this->assertEquals($expected, $result, 'testAbstractOperatorUsesTypesAndSelectorsProperly');

		// ---

		$expected = $this->testSequenceAppliesToItsChildrenInCorrectSequence();
		$result = array(Converter::table()
							   ->td(array(1,1))
							       ->callback('strrev')
							       ->callback('ucfirst')
						   ->transform($this->input),
					    Converter::table()
							   ->td(array(1,1))
							       ->callback('ucfirst')
							       ->callback('strrev')
						   ->transform($this->input)
						);
		$this->assertEquals($expected, $result, 'testSequenceAppliesToItsChildrenInCorrectSequence');

		// ---

		$expected = $this->testTableColDeleteAppliesOnlyToSingleColumn();
		$result = Converter::table()
							   ->col(1)
							       ->delete()
						   ->transform($this->input);
		$this->assertEquals($expected, $result, 'testTableColDeleteAppliesOnlyToSingleColumn');

		// ---

		$expected = $this->testTableColDeleteAppliesToAllColumns();
		$result = Converter::table()
							   ->col()
							       ->delete()
						   ->transform($this->input);
		$this->assertEquals($expected, $result, 'testTableColDeleteAppliesToAllColumns');

		// ---

		$expected = $this->testTableColDeleteAppliesOnlyToSelectedColumns();
		$result = Converter::table()
							   ->col(0,2,3)
							       ->delete()
						   ->transform($this->input);
		$this->assertEquals($expected, $result, 'testTableColDeleteAppliesOnlyToSelectedColumns');

		// ---

	}

	public function testSample1OnReadme()
	{
		
		$data = array(
			array('id' => 0, 'first_name' => 'Foo', 'last_name' => 'Bar'),
			array('id' => 1, 'first_name' => 'Lorem', 'last_name' => 'Ipsum'),
			array('id' => 2, 'first_name' => 'John', 'last_name' => 'Doe'),
		);
		$result = Converter::table()                            //Operating in the table dimension
		                       ->col("id")                      //Select the column "id"
		                           ->delete()                   //And delete it.
		                       ->td(array(null, "first_name"))  //Select the cells from the column "first_name"
		                       	   ->append(" ")                //And append a " " (blank space)
		                       ->col("first_name", "last_name") //Select the "first_name" and "last_name" columns
		                           ->hydrate("name")            //Join them with an array of their two cells as "name"
		                       ->td(array(null, "name"))        //Select the "name" cells from any row
		                           ->callback('implode')        //Implode the containing array
		                       ->col("name")                    //Select the column "name"
		                       	   ->up()                       //Make the selected column the transformed data
		                       ->transform($data);              //Effectively apply the transformations
		                       
		$this->assertEquals(array('Foo Bar', 'Lorem Ipsum', 'John Doe'), $result);
	}
}