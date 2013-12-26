<?php
require 'ArraySorter.class.php';

/*****************************************************
 * Example 1: Apply multiple sort rules
 ****************************************************/
$input = array(
    array(
        'name'  => 'John',
        'age'   => 30
    ),
    array(
        'name'  => 'Jack',
        'age'   => 32
    ),
    array(
        'name'  => 'James',
        'age'   => 30
    ),  
);

// Sort by "age" (ascending) first, then "name" (descending)
$output = ArraySorter::create($input)
            ->addRule('age')
            ->addRule('name', SORT_DESC)
            ->sort();

// $output => James, John, Jack


/*****************************************************
 * Example 2: Case insensitive sorting (PHP 5.4)
 ****************************************************/
$input = array(
    array(
        'name'  => 'john',
        'age'   => 30
    ),
    array(
        'name'  => 'Jack',
        'age'   => 32
    ),
    array(
        'name'  => 'James',
        'age'   => 30
    ),  
);

// Sort by "age" (ascending) then "name" (case insensitive, descending)
$output = ArraySorter::create($input)
            ->addRule('age')
            ->addRule('name', SORT_DESC, SORT_STRING | SORT_FLAG_CASE)
            ->sort();

// $output => James, John, Jack


/*****************************************************
 * Example 3: Sort objects using a class method
 ****************************************************/
$input = array(
    new DateTime('2013-01-01'),
    new DateTime('2014-01-01'),
    new DateTime('2012-01-01')
);

// Sort by dates by seconds since epoch (ascending)
$output = ArraySorter::create($input)->addRule(function($date) {
    // Return the value to be sorted by
    return $date->format('U');
})->sort();

// $output => 2012-01-01, 2013-01-01, 2014-01-01


/*****************************************************
 * Example 4: Sort without modifying array keys
 ****************************************************/
$input = array(
    'John'  => 31,
    'Jack'  => 32,
    'James' => 30
);
// Sort names by value ASC without modyfying keys
$output = ArraySorter::create($input)->sort(TRUE);

// $output => James, John, Jack