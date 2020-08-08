<?php

/**
 * https://xtreamwayz.com/blog/2015-12-30-psr7-abstract-action-factory-one-for-all
 */

namespace App\Factory;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
//use Psr\Container\ContainerInterface;
use ReflectionClass;
use Interop\Container\ContainerInterface;

class AbstractHandlerFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Construct a new ReflectionClass object for the requested action
        $reflection = new ReflectionClass($requestedName);
        // Get the constructor
        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            // There is no constructor, just return a new class
            return new $requestedName;
        }

        // Get the parameters
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            // Get the parameter class
            $class = $parameter->getClass();
            // Get the class from the container
            $dependencies[] = $container->get($class->getName());
        }

        // Return the requested class and inject its dependencies
        return $reflection->newInstanceArgs($dependencies);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // Only accept Action classes
        if (substr($requestedName, -6) == 'Action' ||
            substr($requestedName, -7) == 'Service'||
            substr($requestedName, -7) == 'Handler') {
            return true;
        }

        return false;
    }
}
