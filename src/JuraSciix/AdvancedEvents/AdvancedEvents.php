<?php

namespace JuraSciix\AdvancedEvents;

use JuraSciix\AdvancedEvents\Attribute\EventHandler;
use JuraSciix\AdvancedEvents\Exception\InvalidEventHandlerMethodException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;

final class AdvancedEvents extends PluginBase {

    /**
     * Registers event handlers contained in the given listener class.
     *
     * @param object $listener The listener class.
     * @param Plugin $plugin The plugin that owns the listener.
     *
     * @version 1.1 Code reorganizing, project renaming.
     */
    public static function registerEvents(object $listener, Plugin $plugin): void {
        $listenerClass = new \ReflectionClass($listener);
        $className = $listenerClass->getName();

        if (!$plugin->isEnabled()) {
            throw new PluginException("Disabled plugin tried to register listener $className");
        }

        foreach ($listenerClass->getMethods() as $method) {
            $attributes = $method->getAttributes(EventHandler::class);

            if (!empty($attributes)) {
                /** @var EventHandler $eventHandler */
                $eventHandler = $attributes[0]->newInstance();

                try {
                    self::registerEventHandler($listener, $method, $eventHandler, $plugin);
                } catch (\Exception $e) {
                    $methodString = $className . ($method->isStatic() ? '::' : '->') . $method->getName();
                    throw new PluginException(
                        message: "Couldn't register the $methodString method as an event handler",
                        previous: $e
                    );
                }
            }
        }
    }

    /**
     * @throws \ReflectionException if a reflection error has occurred.
     */
    private static function registerEventHandler(object            $listener,
                                                 \ReflectionMethod $method,
                                                 EventHandler      $eventHandler,
                                                 Plugin            $plugin): void {
        if ($method->isConstructor() || $method->isDestructor() || $method->isGenerator() ||
            $method->isAbstract() || $method->isStatic() || $method->isInternal() ||
            $method->isVariadic()) {
            throw new InvalidEventHandlerMethodException("The method has an invalid declaration");
        }

        if ($method->getNumberOfRequiredParameters() !== 1) {
            throw new InvalidEventHandlerMethodException("The method should have only 1 parameter");
        }

        $parameterType = $method->getParameters()[0]->getType();
        $events = [];

        if ($parameterType instanceof \ReflectionUnionType) {
            foreach ($parameterType->getTypes() as $type) {
                $events[] = $type->getName();
            }
        } else if ($parameterType instanceof \ReflectionNamedType) {
            $events[] = $parameterType->getName();
        } else {
            throw new InvalidEventHandlerMethodException("The method has an invalid parameter type");
        }

        foreach ($events as $event) {
            Server::getInstance()->getPluginManager()->registerEvent(
                $event,
                $method->getClosure($listener),
                $eventHandler->priority,
                $plugin,
                $eventHandler->handleCancelled
            );
        }
    }
}