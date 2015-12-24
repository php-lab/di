<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

/**
 * Dependency injection container for development.
 */
class DevContainer extends Container
{
    /**
     * @var string Suffix for services (objects).
     */
    const DEV_SERVICE_SUFFIX = 'Dev';

    /**
     * @var string Suffix for parameters.
     */
    const DEV_PARAMETER_SUFFIX = '_dev';

    /**
     * Returns a service (object) by name with suffix.
     *
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        $devName = $name . static::DEV_SERVICE_SUFFIX;
        $name = $this->has($devName) ? $devName : $name;

        return parent::get($name);
    }

    /**
     * Returns a parameter by key with suffix.
     *
     * {@inheritDoc}
     */
    public function offsetGet($key)
    {
        $devKey = $key . static::DEV_PARAMETER_SUFFIX;
        $key = $this->offsetExists($devKey) ? $devKey : $key;

        return parent::offsetGet($key);
    }
}
