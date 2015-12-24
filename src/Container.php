<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

/**
 * Dependency injection container.
 */
class Container implements \ArrayAccess
{
    /**
     * @var callable[] Contains service (object) definitions.
     */
    private $services = [];

    /**
     * @var bool[] Contains true for frozen service (object) definitions.
     */
    private $frozenServices = [];

    /**
     * @var mixed[] Contains parameters.
     */
    private $parameters = [];

    /**
     * @var bool[] Contains true for frozen parameters.
     */
    private $frozenParameters = [];

    /**
     * @see Container::get()
     */
    public function __get(string $id)
    {
        return $this->get($id);
    }

    /**
     * @see Container::set()
     * @see Container::setBuilder()
     */
    public function __set(string $id, callable $definition)
    {
        if (ltrim($id, '_') === $id) {
            $this->set($id, $definition);
        } else {
            $this->setBuilder($id, $definition);
        }
    }

    /**
     * Checks if a service (object) is set.
     *
     * @param string $id The unique ID of the service (object).
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * Returns a service (object).
     *
     * @param string $id The unique ID of the service (object).
     *
     * @throws NotFoundException If the service (object) is not defined.
     *
     * @return mixed The service (object) instance or some data.
     */
    public function get(string $id)
    {
        if (isset($this->services[$id])) {
            $this->frozenServices[$id] = true;

            return call_user_func($this->services[$id], $this);
        }
        throw new NotFoundException(sprintf('Service definition "%s" is not defined.', $id));
    }

    /**
     * Sets a service definition.
     *
     * @param string   $id         The unique ID of the service.
     * @param callable $definition The closure or invokable object.
     *
     * @throws FrozenException If the service is frozen.
     *
     * @return $this
     */
    public function set(string $id, callable $definition): self
    {
        if (! isset($this->frozenServices[$id])) {
            $this->services[$id] = function (Container $container) use ($definition) {
                static $service;
                if (!isset($service)) {
                    $service = call_user_func($definition, $container);
                }

                return $service;
            };

            return $this;
        }
        throw new FrozenException(sprintf('Cannot override frozen definition "%s".', $id));
    }

    /**
     * Sets a definition for creating a new object every time.
     *
     * @param string   $id         The unique ID of the object.
     * @param callable $definition The closure or invokable object.
     *
     * @throws FrozenException If the object is frozen.
     *
     * @return $this
     */
    public function setBuilder(string $id, callable $definition): self
    {
        if (! isset($this->frozenServices[$id])) {
            $this->services[$id] = $definition;

            return $this;
        }
        throw new FrozenException(sprintf('Cannot override frozen definition "%s".', $id));
    }

    /**
     * Extends a service (object).
     *
     * @param string   $id        The unique ID of the service (object).
     * @param callable $extension The closure or invokable object.
     *
     * @throws FrozenException   If the service (object) is frozen.
     * @throws NotFoundException If the service (object) is not defined.
     *
     * @return $this
     */
    public function extend(string $id, callable $extension): self
    {
        if (isset($this->frozenServices[$id])) {
            throw new FrozenException(sprintf('Cannot extend frozen definition "%s".', $id));
        }
        if (isset($this->services[$id])) {
            $definition = $this->services[$id];
            $this->services[$id] = function (Container $container) use ($extension, $definition) {
                return call_user_func(
                    $extension,
                    call_user_func($definition, $container),
                    $container
                );
            };

            return $this;
        }
        throw new NotFoundException(sprintf('Service definition "%s" is not defined.', $id));
    }

    /**
     * Removes a service (object).
     *
     * @param string $id The unique ID of the service (object).
     *
     * @return $this
     */
    public function remove(string $id): self
    {
        unset($this->services[$id]);
        unset($this->frozenServices[$id]);

        return $this;
    }

    /**
     * Checks if a parameter is set.
     *
     * @param string $key The unique key of the parameter.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Returns a parameter.
     *
     * @param string $key The unique key of the parameter.
     *
     * @throws NotFoundException If the parameter is not defined.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            $this->frozenParameters[$key] = true;

            return $this->parameters[$key];
        }
        throw new NotFoundException(sprintf('Parameter "%s" is not defined.', $key));
    }

    /**
     * Sets a parameter.
     *
     * @param string $key   The unique key of the parameter.
     * @param mixed  $value The value of the parameter.
     *
     * @throws FrozenException If the parameter is frozen.
     */
    public function offsetSet($key, $value)
    {
        if (! isset($this->frozenParameters[$key])) {
            $this->parameters[$key] = $value;

            return;
        }
        throw new FrozenException(sprintf('Cannot override frozen parameter "%s".', $key));
    }

    /**
     * Removes a parameter.
     *
     * @param string $key The unique key of the parameter.
     */
    public function offsetUnset($key)
    {
        unset($this->parameters[$key]);
        unset($this->frozenParameters[$key]);
    }
}
