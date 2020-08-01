<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event;

use Closure;
use Icanhazstring\Composer\Unused\Event\Exception\ListenerEventTypeResolveException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

class ListenerEventTypeResolver
{
    /**
     * @param object|Closure $listener
     * @throws ListenerEventTypeResolveException
     */
    public function resolve($listener): string
    {
        try {
            $invokeMethodParameters = $this->resolveParameters($listener);
            /** @var ReflectionNamedType|null $invokeMethodParameterType */
            $invokeMethodParameterType = $invokeMethodParameters[0]->getType();

            if ($invokeMethodParameterType === null) {
                throw new ListenerEventTypeResolveException('Typedeclaration for listener invoke missing.');
            }

            return $invokeMethodParameterType->getName();
        } catch (ReflectionException $exception) {
            throw ListenerEventTypeResolveException::fromReflectionException($exception);
        }
    }

    /**
     * @param object|Closure $listener
     *
     * @return ReflectionParameter[]
     * @throws ReflectionException
     */
    private function resolveParameters($listener): array
    {
        if ($listener instanceof Closure) {
            $closureFunction = new ReflectionFunction($listener);
            $closureParameters = $closureFunction->getParameters();

            if (empty($closureParameters)) {
                throw new ListenerEventTypeResolveException(
                    'Expected at least one parameter in closure declaration.'
                );
            }

            return $closureParameters;
        }

        $refClass = new ReflectionClass($listener);
        $invokeMethod = $refClass->getMethod('__invoke');
        $invokeParameters = $invokeMethod->getParameters();

        if (empty($invokeParameters)) {
            throw new ListenerEventTypeResolveException(
                'Expected at least one parameter in __invoke declaration.'
            );
        }

        return $invokeParameters;
    }
}
