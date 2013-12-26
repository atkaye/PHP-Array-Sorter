<?php
require_once __DIR__ . '/../src/ArraySorter.class.php';

class ArraySorterTest extends PHPUnit_Framework_TestCase {

    public function testCreate() {
        $this->assertInstanceOf('ArraySorter', ArraySorter::create(array()));
    }

    /**
     * @depends testCreate
     * @expectedException ArraySorterException
     */
    public function testException() {
        $input = NULL;
        $output = ArraySorter::create($input)->sort();
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

    /**
     * @depends testMultipleRules
     */
    public function testCaseInsensitive() {
        if(defined('SORT_FLAG_CASE')) {
            $person1 = array(
                'name'  => 'jack',
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
                        ->addRule('name', SORT_ASC, SORT_STRING | SORT_FLAG_CASE)
                        ->sort();
            $this->assertEquals($output, array($person1, $person2, $person3)); 
        }
    }
}