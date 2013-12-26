<?php
/**
 * ArraySorter utility package
 * Super simple sorting of arrays in PHP
 * 
 * Example usage:
 * $output = ArraySorter::create($input)->addRule('name')->addRule('age', SORT_DESC)->sort();
 * 
 * @package ArraySorter
 * @version 0.1
 * @author Alex Kaye
 * @link https://github.com/atkaye/PHP-ArraySorter
 */


/**
 * class ArraySorterException
 * @package ArraySorter
 */
class ArraySorterException extends Exception {}


/**
 * class ArraySorter
 * @package ArraySorter
 */
class ArraySorter {
    /**
     * Array to be sorted
     * @var array
     */
    private $array;

    /**
     * Sort rules to be applied
     * @var array
     */
    public $rules = array();


    /**
     * Create a new ArraySorter instance to work with.
     * 
     * @var array $array
     */
    public function __construct($array) {
        if(!is_array($array)) {
            throw new ArraySorterException('$array parameter must be an array');
        }
        $this->array = $array;
    }


    /**
     * Add a sort rule before sorting.
     * 
     * @param NULL|string|callable $method Property name, array index or callable to use for comparison when sorting
     * @param int $dir Direction to sort. Either SORT_ASC or SORT_DESC
     * @param int $flags Additional sort flags, see sort() documentation
     * 
     */
    public function addRule($method = NULL, $dir = SORT_ASC, $flags = SORT_REGULAR) {
        $this->rules[] = array($method, $dir, $flags);
        return $this;
    }


    /**
     * Perform the array sorting according to the conditions added.
     * 
     * @param bool $keep_indexes Whether to maintain indexes on the given array
     * @return array Returns the sorted array
     * @throws ArraySorterException
     */
    public function sort($keep_indexes = FALSE) {
        // Work out which sort function depending whther we need to keep indexes
        $sort_function = $keep_indexes ? 'uasort' : 'usort';
        // Have any rules been added? If not add the defaul rule to sort by value
        if(!count($this->rules)) {
            $this->addRule();
        }
        $sorter = $this;
        if(!call_user_func_array($sort_function, array(&$this->array, function($a, $b) use($sorter) {
            foreach($sorter->rules as $rule) {
                $result = $sorter->valueCompare($a, $b, $rule);
                if($result !== 0) {
                    return $result;
                }
            }
            return 0;
        }))) {
            throw new ArraySorterException('The array could not be sorted');
        }
        // Reset our rules
        $this->rules = array();
        return $this->array;
    }


    /**
     * Compare 2 vars according to the given rule.
     * 
     * Note: The scope of this method is public due to limitations calling protected or private methods on a class instance in PHP 5.3. However it does not provide any useful functionality outside of this class.
     *
     * @param mixed $a First variable to consider
     * @param mixed $b Second variable to consider
     * @param array $rule Array of rule arguments
     * @return Returns an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
     */
    public function valueCompare($a, $b, $rule) {
        list($method, $dir, $flags) = $rule;
        $a_value = $this->value($a, $method);
        $b_value = $this->value($b, $method);
        $compare_result = 0;

        if($this->isSortString($flags)) {
            // Normal string comparison. Should this be case insensitive?
            $compare_result = $this->isSortCaseInsensitive($flags) ? strcasecmp($a_value, $b_value) : strcmp($a_value, $b_value);
        } else if($this->isSortNatural($flags)) {
            // "Natural" human string comparison. Should this be case insensitive?
            $compare_result = $this->isSortCaseInsensitive($flags) ? strnatcasecmp($a_value, $b_value) : strnatcmp($a_value, $b_value);
        } else {
            // Default to SORT_REGULAR / SORT_NUMERIC
            if($a_value < $b_value) {
                $compare_result = -1;
            } else if($a_value > $b_value) {
                $compare_result = 1;
            }
        }

        // Flip the return value if we are ordering DESC
        return $dir == SORT_DESC ? $compare_result * -1 : $compare_result;
    }


    /**
     * Test whether the sort type should be SORT_STRING.
     * @param int $flags
     * @return bool
     */
    protected function isSortString($flags) {
        return  $flags & SORT_STRING;
    }


    /**
     * Test whether the sort type is SORT_NATURAL (requires PHP 5.4).
     * @param int $type
     * @return bool
     */
    protected function isSortNatural($flags) {
        return  defined('SORT_NATURAL') && ($flags & SORT_NATURAL);
    }


    /**
     * Test whether the sort type is case-insensitive (requires PHP 5.4).
     * @param int $flags
     * @return bool
     */
    protected function isSortCaseInsensitive($flags) {
        return  defined('SORT_FLAG_CASE') && ($flags & SORT_FLAG_CASE);
    }


    /**
     * Get the value of a comparison variable according to the given $method.
     *
     * @param mixed $var Variable to retrieve value from using $method
     * @param mixed $method Method used to retrive comparison value for $var
     */
    protected function value($var, $method = NULL) {
        if(is_callable($method)) {
            return call_user_func_array($method, array($var));
        } else if(is_string($method)) {
            if(is_object($var)) {
                return $var->$method;
            } elseif(is_array($var)) {
                return $var[$method];
            }
        }
        return $var;
    }


    /**
     * Create an instance of ArraySorter. Facilitates chaining and sorting in a one-liner.
     * 
     * @param array $array
     * @return static
     */
    public static function create($array) {
        if(!is_array($array)) {
            throw new ArraySorterException('$array parameter must be an array');
        }
        $class = __CLASS__;
        return new $class($array);
    }
}