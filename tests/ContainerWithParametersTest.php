<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

class ContainerWithParametersTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testShouldThrowExceptionIfParameterNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $param = $this->container['param.not_exists'];
    }

    public function testShouldAssertWhatParameterNotExists()
    {
        $result = isset($this->container['param.not_exists']);
        $this->assertFalse($result);
    }

    public function testShouldAssertWhatParameterExists()
    {
        $this->container['param.test_value'] = 'value';
        $result = isset($this->container['param.test_value']);
        $this->assertTrue($result);
    }

    public function testShouldGetParameterValue()
    {
        $this->container['param.test_value'] = 'value';
        $param = $this->container['param.test_value'];
        $this->assertEquals('value', $param);
    }

    public function testShouldGetParameterAfterChangeValue()
    {
        $this->container['param.test_value'] = 'value';
        $this->container['param.test_value'] = 'another value';
        $param = $this->container['param.test_value'];
        $this->assertEquals('another value', $param);
    }

    public function testShouldThrowExceptionIfChangeValueAfterGettingParameter()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container['param.test_value'] = 'value';
        $param = $this->container['param.test_value'];
        $this->container['param.test_value'] = 'another value';
    }

    public function testShouldRemoveParameter()
    {
        $this->container['param.test_value'] = 'value';
        unset($this->container['param.test_value']);
        $result = isset($this->container['param.test_value']);
        $this->assertFalse($result);
    }

    public function testShouldGetProtectedAnonymousFunction()
    {
        $this->container['func.protected'] = function () {
            return 'value';
        };
        $result = $this->container['func.protected']();
        $this->assertEquals('value', $result);
    }
}
