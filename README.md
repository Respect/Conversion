Respect\Conversion
==================

The most awesome conversion engine ever written for PHP.

Featuring:

  * Built-in support for many data formats: tree, collection, table, chart, image, xml, etc.
  * Fluent interface like `Converter::table()->col(2)->remove();` and `Converter::chart()->rowSeries("Income")->line("red")`
  * Seamless convertion between formats: tree to collection, table to tree, collection to chart, collection to xml, chart to image, etc...

Installation
------------

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
	                       ->col("name")                    //Select the "name" column recently created
	                           ->callback('implode');       //Implode the containing array
	                       ->col("name")                    //Select the column "name"
	                       	   ->up()                       //Make the selected column the transformed data
	                       ->transform($data);              //Effectively apply the transformations
	print_r($result); //array('Foo Bar', 'Lorem Ipsum', 
	                                          'John Doe');  //The Result

In the sample above, we've operated in the table dimension to transform
data and extract the raw full names from a table array.

Respect\Conversion works on four main component types:

  * **Operators**, that really to the conversion work.
  * **Selectors**, that allow targeting the data being converted.
  * **Types**, the converting dimensions of data being transformed.
  * **The Converter**, that knows how to use the components above.

Let's try a hardcore sample:

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



### Collection Dimension
### Tree Dimension
### Table Dimension
### Xml Dimension
### Chart Dimension
### Image Dimension
