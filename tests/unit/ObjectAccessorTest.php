<?php

/**
 * Tests the CubeObject\ObjectAccessor trait
 *
 * PHP version 5.4
 *
 * @vendor     27 Cubes
 * @package    CubeObject
 * @subpackage Tests
 * @author     27 Cubes <info@27cubes.net>
 * @license    http://www.wtfpl.net WTFPL
 * @since      %NEXT_VERSION%
 */

namespace CubeObject\Tests;
use CubeObject\ObjectAccessor;

class TraitTester
{
    use ObjectAccessor;

    public $id;
    public $name;
    public $item;
    public $active = false;

    protected function lazyLoadItem()
    {
        $this->item = $this->name . '-' . $this->id;
    }
}

class ObjectAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testCall_InvalidMethod()
    {
        $obj = new TraitTester();
        $obj->someNonExistentMethod();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGet_InvalidProperty()
    {
        $obj = new TraitTester();
        $obj->getSomeNonExistentProperty();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testHas_InvalidProperty()
    {
        $obj = new TraitTester();
        $obj->hasSomeNonExistentProperty();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testIs_InvalidProperty()
    {
        $obj = new TraitTester();
        $obj->isSomeNonExistentProperty();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testIs_NonBoolProperty()
    {
        $obj = new TraitTester();
        $obj->isId();
    }

    public function testAccessorMethods()
    {
        $id = rand(1, 999999);
        $name = uniqid('object name');
        $obj = new TraitTester();
        // Initial state get*(), has*(), is*()
        $this->assertEmpty($obj->getId());
        $this->assertEmpty($obj->getName());
        $this->assertEmpty($obj->getActive());
        $this->assertFalse($obj->hasId());
        $this->assertFalse($obj->hasName());
        $this->assertFalse($obj->hasActive());
        $this->assertFalse($obj->isActive());
        // Set some property values
        $obj->id = $id;
        $obj->name = $name;
        $obj->active = true;
        // get*(), has*(), is*()
        $this->assertEquals($id, $obj->getId());
        $this->assertEquals($name, $obj->getName());
        $this->assertTrue($obj->getActive());
        $this->assertTrue($obj->hasId());
        $this->assertTrue($obj->hasName());
        $this->assertTrue($obj->hasActive());
        $this->assertTrue($obj->isActive());
        // Switch bool field back to false
        $obj->active = false;
        $this->assertFalse($obj->hasActive());
        $this->assertFalse($obj->isActive());
    }

    public function testGet_LazyLoadMethod()
    {
        $id = rand(1, 999999);
        $name = uniqid('object name');
        $item = $name . '-' . $id;
        $obj = new TraitTester();
        $obj->id = $id;
        $obj->name = $name;
        $this->assertEquals($id, $obj->getId());
        $this->assertEquals($name, $obj->getName());
        $this->assertEquals($item, $obj->getItem());
    }
}
