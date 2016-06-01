PHP Array Sorter
===============

Super simple sorting of arrays in PHP

 [![Build Status](https://secure.travis-ci.org/atkaye/PHP-ArraySorter.png)](http://travis-ci.org/atkaye/PHP-ArraySorter)

## Features
- Sort multi-dimmensional arrays or arrays of objects
- [Apply multiple ordering rules (similar to MySQL)][multiple-rules]
- [Case-insensitve sorting (PHP 5.4)][case-insensitive]
- [Sort using a closure or class method][closure]
- [Sort without modifying array keys][keep-keys]

### Apply multiple ordering rules (similar to MySQL)
```php
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
            
// $output => John, James, Jack
```

### Case-insensitve sorting (PHP 5.4)
```php
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
            
// $output => jonn, James, Jack
```

### Sort using a closure or class method
```php
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
```

### Sort without modifying array keys
```php
$input = array(
    'John'  => 31,
    'Jack'  => 32,
    'James' => 30
);
// Sort names by value ASC without modifying keys
$output = ArraySorter::create($input)->sort(TRUE);

// $output => James, John, Jack
```
## Coming Soon
- Pass array key to closure function
- Support for pass-by-reference
- Performance improvements
- Case-insensitive sorting for PHP 5.3
- Add Documentation for `SORT_NATURAL`
- PHPUnit tests

## Current Version
0.1.1

## License
[MIT][license]

[multiple-rules]:#apply-multiple-ordering-rules-similar-to-mysql
[case-insensitive]:#case-insensitve-sorting-php-54
[closure]:#sort-using-a-closure-or-class-method
[keep-keys]:#sort-without-modifying-array-keys
[license]:https://github.com/atkaye/PHP-ArraySorter/blob/master/LICENSE
