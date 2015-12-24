<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di\Fake;

/**
 * Fake component.
 */
class Component implements ComponentInterface
{
    protected $func;

    public function __construct(callable $func)
    {
        $this->func = $func;
    }

    public function getResult(string $value)
    {
        return call_user_func($this->func, $value);
    }
}
