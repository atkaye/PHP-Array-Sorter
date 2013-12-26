<?php
require_once __DIR__ . '/../src/ArraySorter.class.php';

class ArraySorterTest extends PHPUnit_Framework_TestCase {

    public function testCreate() {
        $this->assertInstanceOf('ArraySorter', ArraySorter::create(array()));
    }

    /**
     * @depends testCreate
     */
    public function testByValue() {
        $input = array(4, 2, 3, 1);
        $output = ArraySorter::create($input)->sort();
        $this->assertEquals($output, array(1, 2, 3, 4));
    }

    /**
     * @depends testByValue
     */
    public function testDesc() {
        $input = array(4, 2, 3, 1);
        $output = ArraySorter::create($input)->addRule(NULL, SORT_DESC)->sort();
        $this->assertEquals($output, array(4, 3, 2, 1));
    }

    /**
     * @depends testByValue
     */
    public function testSortRegular() {
        // SORT_STRING
        /*
        $a = '.5';
        $b = '0.3';
        $c = 4;
        $d = '4';
        $e = 'F';
        $f = 'z';
        $input = array(
            $d, $e, $a, $f, $b, $c
        );
        sort($input, SORT_STRING);
        */

        // SORT_REGULAR
        
        $a = '0.3';
        $b = '.5';
        $c = '4';
        $d = 'F';
        $e = 'z';
        $f = 4;
        $input = array(
            //$d, $e, $b, $f, $c, $a
            $d, $e, $a, $f, $b, $c
        );
        sort($input, SORT_REGULAR);
        

        // SORT_NUMERIC
        /*
        $a = 'F';
        $b = 'z';
        $c = '0.3';
        $d = '.5';
        $e = 4;
        $f = '4';
        $input = array(
            $d, $e, $b, $f, $c, $a
        );
        sort($input, SORT_NUMERIC);
        */
        
        /*
        $output = ArraySorter::create($input)->sort(NULL, SORT_ASC, SORT_NUMERIC);
        */
        $this->assertEquals($input, array($a, $b, $c, $d, $e, $f));
    }

    /**
     * @depends testByValue
     */
    public function testKeepKeys() {
        $input = array(
            'a' => 4,
            'b' => 2,
            'c' => 3,
            'd' => 1
        );
        $output = ArraySorter::create($input)->sort(TRUE);
        $this->assertEquals($output, array(
            'd' => 1,
            'b' => 2,
            'c' => 3,
            'a' => 4
        ));
    }

    /**
     * @depends testCreate
     */
    public function testClosure() {
        $date1 = new DateTime('2012-01-01');
        $date2 = new DateTime('2013-01-01');
        $date3 = new DateTime('2014-01-01');
        
        $input = array(
            $date2, $date3, $date1
        );

        $self = $this;
        $output = ArraySorter::create($input)->addRule(function($date) use($self) {
            $self->assertInstanceOf('DateTime', $date);
            return $date->format('U');
        })->sort();
        
        $this->assertEquals($output, array(
            $date1, $date2, $date3
        ));
    }

    /**
     * @depends testCreate
     */
    public function testNestedArrayByProperty() {
        $person1 = array(
            'name'  => 'Jack'
        );
        $person2 = array(
            'name'  => 'James'
        );
        $person3 = array(
            'name'  => 'John'
        );

        $input = array($person3, $person1, $person2);
        $output = ArraySorter::create($input)
                    ->addRule('name')
                    ->sort();
        $this->assertEquals($output, array($person1, $person2, $person3));             
    }

    /**
     * @depends testCreate
     */
    public function testNestedObjectsByMember() {
        $person1 = (object) array(
            'name'  => 'Jack'
        );
        $person2 = (object) array(
            'name'  => 'James'
        );
        $person3 = (object) array(
            'name'  => 'John'
        );

        $input = array($person3, $person1, $person2);
        $output = ArraySorter::create($input)
                    ->addRule('name')
                    ->sort();
        $this->assertEquals($output, array($person1, $person2, $person3));             
    }    

    /**
     * @depends testNestedArrayByProperty
     */
    public function testMultipleRules() {
        $person1 = array(
            'name'  => 'Jack',
            'age'   => 30
        );
        $person2 = array(
            'name'  => 'James',
            'age'   => 30
        );
        $person3 = array(
            'name'  => 'John',
            'age'   => 32
        );

        $input = array($person3, $person1, $person2);
        $output = ArraySorter::create($input)
                    ->addRule('age')
                    ->addRule('name')
                    ->sort();
        $this->assertEquals($output, array($person1, $person2, $person3)); 
    }
}