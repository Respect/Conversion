Respect\Conversion
==================

The most awesome conversion engine ever written for PHP.

Featuring:

  * Built-in support for many data formats: tree, collection, table, chart, image, xml, etc.
  * Fluent interface like `Converter::table()->col(2)->remove();` and `Converter::chart()->rowSeries("Income")->line("red")`
  * Seamless convertion between formats: tree to collection, table to tree, collection to chart, collection to xml, chart to image, etc...

Disclaimer: Not all features described here are implemented, some of them are incomplete and
unfinished. They're all marked as TODO when appropriate.

Installation
------------

For now, clone our repo and set up a PSR-0 autoloader for the source folder. Composer
and PEAR packages are on their way!

Configuration
-------------

Just import our namespace:

	<?php
	use Respect\Conversion\Converter;

Feature Guide
-------------

Let's start with a small sample. We're going to remove the second item
from an array:

	$data = array('foo', 'bar', 'baz');     //Data being transformed
	$result = Converter::collection()       //Type dimension
			               ->item(2)        //Selector
			                   ->delete()   //Operator
			           ->transform($data);  //The conversion method

	print_r($result); //array('foo', 'bar') //The result

The type dimensions tell selectors and operators how to behave. The `delete()`
operator for instance needs to know if it is acting on a table row, or a
collection item, or a XML tag.

Selectors tell operators in which part of the data input they're gonna operate.
In the sample above, we're going to operate on the third item of a collection.

Operators, finally, act on a selected part of the input data and transform it.

Let's see a better sample:

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
	print_r($result); //array('Foo Bar', 'Lorem Ipsum',     //
	                                          'John Doe');  //The Result

In the sample above, we've operated in the table dimension to transform
data and extract the raw full names from a table array.

Respect\Conversion works on four main component types:

  * **Operators**, that really to the conversion work.
  * **Selectors**, that allow targeting the data being converted.
  * **Types**, the converting dimensions of data being transformed.
  * **The Converter**, that knows how to use the components above.

Let's try a hardcore sample (illustrative, code not implemented yet for this):

	$data = array(
		'All' => array('investment' => 32.0, 'income' => 26.5)
		'A'   => array('investment' => 12.0, 'income' => 6.5),
		'B'   => array('investment' => 9.0,  'income' => 5.5),
	);
	$result = Converter::chart()                 //Operate the table as a dataseries for a chart
	                       ->rowSeries("All")    //Select the first row series
	                       	   ->line("#000", 3) //Make it a black chart line with 3px width
	                       ->rowSeries("A", "B") //Select all row series
	                           ->line(null, 1)   //Make them a line chart with random color and 1px width
	                   ->image()                 //Operate on the image dimension
	                   	   ->background()        //Select the image background
	                   	       ->color("white")  //Make the background white (default is transparent)
	                       ->frames()            //Select all frames (all lines from chart)
	                           ->flatten()       //Merge lines into a single image
	                   	   ->canvas()            //Select the canvas
	                   	       ->grow(20, 20)    //Grow it by 20px, simulating a 10px border
	                   	   ->format()            //Select the format
	                   	       ->type('png')     //Make it PNG
	                   ->transform($data);       //Effectively apply the transformations

This will generate this image:

	//TODO CODE AND SAMPLE

### Collection Dimension

On the Collection dimension we operate on a list of things. Possible selectors and operators:

#### Item

Selects all items.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item()               //Selects all items
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations

#### Item($key[, $key[, $key...]])

Selects an item from its key.

	$data = array('foo', 'bar', 'baz', 'lorem' => 'ipsum'); 

	$result = Converter::collection()             //Collection dimension
                           ->item(1)              //Selects only the item 1 (bar)
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations

Another Selector samples are:

			  		       ->item("lorem")        //Selects only the item named lorem

			  		       ->item("lorem", 0)     //Selects the item lorem and the item 0

#### Item($callback[, $callback[, $callback...]])

Selects an item matching a callback function.

	$data = array('foo', 'bar', 'baz', 'lorem' => 'ipsum'); 
	$strlen3   = function($item) { return strlen($item) === 3; };
	$containsr = function($item) { return false !== stripos('r', $item); };

	$result = Converter::collection()             //Collection dimension
			               ->item($strlen3)       //Selects only items with 3 letters in value
			  		     	  ->{$operator}()     //Apply some operator
			  		   ->transform($data);        //Effectively apply the transformations

Another selector samples:
			               ->item($containsr)     //Selects only items with the 'r' letter

			               ->item($strlen3, 
				                     $containsr)  //Selects only items with three letters and an 'r'

Using Respect\Validation `create()` utility you can create callbacks
that matches several patterns including `v::odd()`, `v::even()`, `v::multipleOf()` and
more specific ones like `v::object()->attribute('name')` (item must be an object with the attribute
name).

    use Respect\Validation\Validator as v;

    $validateOdd = v::create(null, null, v::odd()); //closure for $item, $key, $lineNo

	$result = Converter::collection()             //Collection dimension
				           ->item($validateOdd)   //Selects only odd items
			  		     	  ->{$operator}()     //Apply some operator
			  		   ->transform($data);        //Effectively apply the transformations


#### Item($mixed[, $mixed[, $mixed...]])

	$result = Converter::collection()             //Collection dimension
			               ->item(0, $cb2)        //Selects the item 0 and elements with the 'r' letter
			  		     	  ->{$operator}()     //Apply some operator
			  		   ->transform($data);        //Effectively apply the transformations

#### Item(x) + Item(y) == Item(x,y)

An item called after an item...

	Converter::collection()
	             ->item(0)
	             ->item(1)

...binds to an item with both their arguments equivalent to:

	Converter::collection()
	             ->item(0,1)

Bounding also occours with Table\First and Table\Last.

#### Item\Append($string)

Appends a string at the end of the item value.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item()               //Selects all items
                               ->append('0')      //Appends a zero at the end
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('foo0', 'bar0', 'baz0')

See also:

  * Item\Prepend - Prepends strings to items in a collection
  * Td\Prepend - Prepends strings to cells in a table
  * Td\Append - Appends strings to cells in a table
  * Xpath\Append - Appends strings to XPath results. //TODO
  * Xpath\Prepend - Prepends strings to XPath results. //TODO

#### Item\Callback($callback)

Applies a callback at any item.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item()               //Selects all items
                               ->callback(
	                               		'strrev') //Reverses their content
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('oof', 'rab', 'zab')

See also:

  * Col\Callback - Applies a callback to table columns.
  * Tr\Callback - Applies a callback to table rows.
  * Td\Callback - Applies a callback to table cells.
  * Leaf\Callback - Applies a callback to tree leaves.
  * Branch\Callback - Applies a callback to tree branches.
  * Xpath\Callback - Applies a callback to XPath results. //TODO

#### Item\Delete

Deletes items.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item(1)              //Selects item 1
                               ->delete()         //Deletes it
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('foo', 'baz')

See also:

  * Col\Delete - Deletes columns from a table
  * Td\Delete - Deletes cells from a table
  * Tr\Delete - Delete rows from a table
  * Leaf\Delete - Delete leaves from tree //TODO
  * Branch\Delete - Delete branches from a tree //TODO
  * Xpath\Delete - Deletes matching XPath results //TODO

#### Item\Extract

Extracts inner arrays to the main collection.

	$data = array('foo', 'bar', 'lorem' => 'ipsum'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item('lorem')        //Selects item lorem
                               ->extract()        //Extracts their values to the collection
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('foo', 'bar', 'lorem' => 'ipsum')

#### Item\Name

Names items in the collection.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item('foo')          //Selects item lorem
                               ->name('zero')     //Extracts their values to the collection
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('zero' => foo', 'bar', 'baz')

See also:

  * Col\Name - Names columns in a table
  * Td\Name - Names cells in a table
  * Tr\Name - Name rows in a table

#### Item\Prepend($string)

Prepends a string at the beginning of the item value.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item()               //Selects all items
                               ->append('0')      //Appends a zero at the end
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //array('0foo', '0bar', '0baz')

See also:

  * Item\Append - Appends strings to items in a collection
  * Td\Prepend - Prepends strings to cells in a table
  * Td\Append - Appends strings to cells in a table
  * Xpath\Append - Appends strings to XPath results. //TODO
  * Xpath\Prepend - Prepends strings to XPath results. //TODO

#### Item\Up

Promotes an item to the main transformation target.

	$data = array('foo', 'bar', 'baz'); //Sample data

	$result = Converter::collection()             //Collection dimension
                           ->item(1)              //Selects all items
                               ->up()             //Appends a zero at the end
                       ->transform($data);        //Effectively apply the transformations
                    
    print_r($result); //(string) 'bar'

See also:

  * Col\Up - Promotes table columns
  * Tr\Up - Promotes table rows
  * Td\Up - Promotes table cells //TODO
  * Leaf\Up - Promotes tree leaves //TODO
  * Branch\Up - Promotes tree branches //TODO
  * Xpath\Up - Promotes XPath results //TODO

#### First

Selects the first item of the collection. Same operators and bindings as Table\Item.

#### Last

Selects the last item of the collection. Same operators and bindings as Table\Item;

### Tree Dimension

On the Tree dimension we operate on a hierarchical set of arrays. Possible selectors and operators:

#### Leaf

Selects all leaves from the tree. Leaves are scalar values (integers, floats, strings) that
can't "branch" more.

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum'))); 

	$result = Converter::tree()                   //Collection dimension
                           ->leaf()               //Selects all leaves (foo, bar, lorem, ipsum)
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations

#### Leaf($value[, $value[, $value...]])

Selects leaves that match some value. 

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum', 'bar'))); 

	$result = Converter::tree()                   //Collection dimension
                           ->leaf('bar')          //Selects the two leaves matching bar
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations


#### Leaf($callback[, $callback[, $callback...]])

Selects leaves that match a specific callback from the tree.

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum', 'bar'))); 
	$strlen3 = function($leaf) { return strlen($leaf) === 3 };

	$result = Converter::tree()                   //Collection dimension
                           ->leaf($strlen3)       //Selects leaves with strlen=3
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations

#### Leaf($mixed[, $mixed[, $mixed...]])

Mix values and callbacks when selecting leaves.

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum', 'bar'))); 
	$strlen3 = function($leaf) { return strlen($leaf) === 3 };
	
	$result = Converter::tree()                   //Collection dimension
                           ->leaf($strlen3, 
	                                     "ipsum") //Selects leaves with strlen=3 and "ipsum"
                               ->{$operator}()    //Apply some operator
                       ->transform($data);        //Effectively apply the transformations

#### Leaf(x) + Leaf(y) == Leaf(x, y)

A leaf called after a leaf...

	Converter::tree()
	             ->leaf("active")
	             ->leaf("ativo")

...binds to a leaf with both their arguments equivalent to:

	Converter::tree()
	             ->leaf("active", "ativo")

#### Leaf\Callback($callback)

Applies callbacks on leaves from a tree.

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum'))); 

	$result = Converter::tree()                   //Collection dimension
                           ->leaf()               //Selects all leaves (foo, bar, lorem, ipsum)
                               ->callback(
	                               		'strrev') //Reverse them
                       ->transform($data);        //Effectively apply the transformations

    print_r($result); //array('oof', array('rab', 'baz' => array('merol', 'muspi')))

See also:

  * Branch\Callback - Applies a callback to tree branches.
  * Col\Callback - Applies a callback to table columns.
  * Tr\Callback - Applies a callback to table rows.
  * Td\Callback - Applies a callback to table cells.
  * Item\Callback - Applies a callback to collection items.
  * Xpath\Callback - Applies a callback to XPath results. //TODO

#### Branch

Selects all branches from the tree. Branches are structures that hold one ore more leaves.

	//TODO DOCS

#### Branch($value[, $value[, $value...]])

Selects branches that match some value. 

	//TODO DOCS

#### Branch($callback[, $callback[, $callback...]])

Selects branches that match a specific callback from the tree.

	//TODO DOCS

#### Branch($mixed[, $mixed[, $mixed...]])

Mix values and callbacks when selecting branches.

	//TODO DOCS

#### Branch(x) + Branch(y) == Branch(x, y)

A branch called after a branch...

	Converter::tree()
	             ->branch(v::attribute('name'))
	             ->branch(v::attribute('nombre'))

...binds to a branch with both their arguments equivalent to:

	Converter::tree()
	             ->branch(v::attribute('name'), 
	             		  v::attribute('nombre'))

#### Branch\Callback($callback)

Applies callbacks on leaves from a tree.

	$data = array('foo', array('bar', 'baz' => array('lorem', 'ipsum'))); 

	$result = Converter::tree()                   //Collection dimension
                           ->branc()              //Selects all branches
                               ->callback(
	                               	   'implode') //Implode them
                       ->transform($data);        //Effectively apply the transformations

    print_r($result); //array('foo', 'barloremipsum')

See also:

  * Leaf\Callback - Applies a callback to tree leaves.
  * Col\Callback - Applies a callback to table columns.
  * Tr\Callback - Applies a callback to table rows.
  * Td\Callback - Applies a callback to table cells.
  * Item\Callback - Applies a callback to collection items.
  * Xpath\Callback - Applies a callback to XPath results. //TODO

### Table Dimension

The table dimension operates on a set of rows and columns represented
by a multi-dimensional vector of associative arrays.

Possible selectors and operators:

#### Col

Selects all columns from the table.

	//TODO DOCS

#### Col($key[, $key[, $key...]])

Selects all columns matching a key or position number from the table.

	//TODO DOCS

#### Col($callback[, $callback[, $callback...]])

Selects all columns matching a callback.

	//TODO DOCS

#### Col($mixed[, $mixed[, $mixed...]])

Mix position, key and callback when selecting columns.

	//TODO DOCS

#### Col(x) + Col(y) == Col(x, y)

A col called after a col...

	Converter::table()
	             ->col('name')
	             ->col('nombre')

...binds to a col with both their arguments equivalent to:

	Converter::table()
	             ->col('name', 'nombre')

#### Col(x) + Tr(y) == Td(array(y, x))

A tr called after a col...

	Converter::table()
	             ->col('name')
	             ->tr(0)

...binds to a td with both their arguments equivalent to:

	Converter::table()
	             ->td(array(0, 'name'));

#### Tr

Selects all rows from the table.s

	//TODO DOCS

#### Tr($key[, $key[, $key...]])

Selects all rows matching a key or position number from the table.

	//TODO DOCS

#### Tr($callback[, $callback[, $callback...]])

Selects all rows matching a callback.

	//TODO DOCS

#### Tr($mixed[, $mixed[, $mixed...]])

Mix position, key and callback when selecting rows.

	//TODO DOCS

#### Tr(x) + Tr(y) == Tr(x, y)

A tr called after a tr...

	Converter::table()
	             ->tr('name')
	             ->tr('nombre')

...binds to a tr with both their arguments equivalent to:

	Converter::table()
	             ->tr('name', 'nombre')

#### Tr(y) + Col(x) == Td(array(y, x))

A tr called after a col...

	Converter::table()
	             ->col('name')
	             ->tr(0)

...binds to a td with both their arguments equivalent to:

	Converter::table()
	             ->td(array(0, 'name'));
#### Td

Selects all cells from a table.

	//TODO DOCS

#### Td(array($row, $col)[, array($row, $col)[, array($row, $col)...]])

Selects specific cells from a table.

	//TODO DOCS

#### Td(array(null, $col)[, array(null, $col)[, array(null, $col)...]])

Selects specific cells from a column in a table.

	//TODO DOCS

#### Td(array($row, null)[, array($row, null)[, array($row, null)...]])

Select specific cells from a row in a table.

	//TODO DOCS

#### Td($mixed[, $mixed[, $mixed...]])

Mix any of the styles above when selecting cells on a table.

	//TODO DOCS

#### Td(x) + Td(y) == Td(x, y)

A td called after a td...

	Converter::table()
	             ->td(array(0,1))
	             ->td(array(1,1))

...binds to a td with both their arguments equivalent to:

	Converter::table()
	             ->td(array(0,1), array(1,1))
### Xml Dimension

The XML dimension operates on a DOMDocument.

	//TODO DOCS AND CODE

### Chart Dimension

The Chart dimension operates on one ore more ImagickDraw objects representing
chart elements.

	//TODO DOCS AND CODE

### Image Dimension

The Image dimension operates on one ore more Imagick objects representing
image elements.

	//TODO DOCS AND CODE
