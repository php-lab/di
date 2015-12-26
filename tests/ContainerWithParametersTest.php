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
    protected $app;

    public function setUp()
    {
        $this->app = new Container();
    }

    public function testShouldThrowExceptionIfParameterNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $param = $this->app['param.not_exists'];
    }

    public function testShouldAssertWhatParameterNotExists()
    {
        $result = isset($this->app['param.not_exists']);
        $this->assertFalse($result);
    }

    public function testShouldAssertWhatParameterExists()
    {
        $this->app['param.test_value'] = 'value';
        $result = isset($this->app['param.test_value']);
        $this->assertTrue($result);
    }

    public function testShouldGetParameterValue()
    {
        $this->app['param.test_value'] = 'value';
        $param = $this->app['param.test_value'];
        $this->assertEquals('value', $param);
    }

    public function testShouldGetParameterAfterChangeValue()
    {
        $this->app['param.test_value'] = 'value';
        $this->app['param.test_value'] = 'another value';
        $param = $this->app['param.test_value'];
        $this->assertEquals('another value', $param);
    }

    public function testShouldThrowExceptionIfChangeValueAfterGettingParameter()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app['param.test_value'] = 'value';
        $param = $this->app['param.test_value'];
        $this->app['param.test_value'] = 'another value';
    }

    public function testShouldRemoveParameter()
    {
        $this->app['param.test_value'] = 'value';
        unset($this->app['param.test_value']);
        $result = isset($this->app['param.test_value']);
        $this->assertFalse($result);
    }

    public function testShouldGetProtectedAnonymousFunction()
    {
        $this->app['func.protected'] = function () {
            return 'value';
        };
        $result = $this->app['func.protected']();
        $this->assertEquals('value', $result);
    }
}
