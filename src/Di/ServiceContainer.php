<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Di;

use Exception;
use Icanhazstring\Composer\Unused\Di\Exception\ServiceNotCreatedException;
use Icanhazstring\Composer\Unused\Di\Exception\ServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class ServiceContainer implements ContainerInterface
{
    private $factories = [];
    private $services = [];

    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        return $this->services[$name] = $this->doCreate($name);
    }

    public function has($name): bool
    {
        return isset($this->services[$name]);
    }

    /**
     * @return mixed
     */
    public function build(string $name, array $options = [])
    {
        return $this->doCreate($name, $options);
    }

    /**
     * @param string $name
     * @param mixed  $object
     */
    public function register(string $name, $object): void
    {
        $this->services[$name] = $object;
    }

    private function configure(array $config): void
    {
        $this->factories = $config['factories'] ?? [];
    }

    /**
     * @return mixed
     */
    private function doCreate(string $name, array $options = [])
    {
        if (!isset($this->factories[$name])) {
            throw new ServiceNotFoundException(
                sprintf('Could not resolve %s to a factory.', $name)
            );
        }

        try {
            $factory = $this->getFactory($name);
            $object = $factory($this, $options);
        } catch (ContainerExceptionInterface $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new ServiceNotCreatedException(sprintf(
                'Service with name "%s" could not be created. Reason: %s',
                $name,
                $exception->getMessage()
            ), (int)$exception->getCode(), $exception);
        }

        return $object;
    }

    /**
     * @return mixed|null
     */
    private function getFactory(string $name)
    {
        $factory = $this->factories[$name] ?? null;
        $lazyLoaded = false;

        if (is_string($factory) && class_exists($factory)) {
            $factory = new $factory();
            $lazyLoaded = true;
        }

        if (is_callable($factory)) {
            if ($lazyLoaded) {
                $this->factories[$name] = $factory;
            }

            return $factory;
        }

        throw new ServiceNotFoundException(sprintf(
            'Unable to resolve service "%s" to a factory; are you certain you provided it during configuration?',
            $name
        ));
    }
}
