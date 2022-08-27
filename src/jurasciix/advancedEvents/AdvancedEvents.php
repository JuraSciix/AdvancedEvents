<?php

declare(strict_types=1);

namespace jurasciix\advancedEvents;

use jurasciix\advancedEvents\attribute\EventHandler;
use jurasciix\advancedEvents\exception\InvalidEventHandlerMethodException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;
use pocketmine\utils\Utils;

class AdvancedEvents extends PluginBase {

    /**
     * Registers the given event listener.
     *
     * @param object $listener Event listener instance.
     * @param Plugin $plugin The plugin that owns the given event listener.
     *
     * @deprecated Use AdvancedEvents#registerEventHandler() instead.
     */
    public static function registerEvents(object $listener, Plugin $plugin): void {
        self::registerEventListener($listener, $plugin);
    }

    /**
     * Registers the given event listener.
     *
     * @param object $listener Event listener instance.
     * @param Plugin $plugin The plugin that owns the given event listener.
     */
    public static function registerEventListener(object $listener, Plugin $plugin): void {
        $listenerClass = new \ReflectionClass($listener);
        $className = $listenerClass->getName();

        if (!$plugin->isEnabled()) {
            throw new PluginException("Disabled plugin tried to register listener: $className");
        }

        foreach ($listenerClass->getMethods() as $method) {
            $attributes = $method->getAttributes(EventHandler::class);

            if (empty($attributes)) {
                continue;
            }

            /** @var EventHandler $eventHandler */
            $eventHandler = $attributes[0]->newInstance();

            try {
                self::registerEventHandler($listener, $method, $eventHandler, $plugin);
            } catch (\Exception $e) {
                $niceMethodName = Utils::getNiceClosureName($method->getClosure($listener));
                throw new PluginException(
                    message: "Couldn't register the $niceMethodName method as an event handler",
                    previous: $e
                );
            }
        }
    }

    private static function registerEventHandler(object            $listener,
                                                 \ReflectionMethod $method,
                                                 EventHandler      $eventHandler,
                                                 Plugin            $plugin): void {
        if ($method->isConstructor() || $method->isDestructor() || $method->isGenerator() ||
            $method->isAbstract() || $method->isStatic() || $method->isInternal() ||
            $method->isVariadic()) {
            throw new InvalidEventHandlerMethodException("Method has illegal declaration");
        }

        if ($method->getNumberOfRequiredParameters() !== 1) {
            throw new InvalidEventHandlerMethodException("Method must have only 1 required parameter");
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
            throw new InvalidEventHandlerMethodException("Method has an illegal parameter type");
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