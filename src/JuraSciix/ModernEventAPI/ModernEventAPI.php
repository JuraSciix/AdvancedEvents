<?php

namespace JuraSciix\ModernEventAPI;

use JuraSciix\ModernEventAPI\Attribute\EventHandler;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;

class ModernEventAPI extends PluginBase {

    public static function registerEvents(Listener $listener, Plugin $plugin): void {
        if (!$plugin->isEnabled()) {
            throw new PluginException('Plugin tried to register class ' . get_class($listener) . ' as listener while disabled');
        }
        $pluginManager = Server::getInstance()->getPluginManager();

        foreach ((new \ReflectionClass($listener))->getMethods() as $method) {
            $eventHandler = static::resolveEventHandler($method);

            if ($eventHandler === null) {
                continue;
            }

            if ($method->isConstructor() || $method->isDestructor() || $method->isAbstract() || $method->isStatic() || $method->isInternal()) {
                throw new PluginException('Event-handler method ' . get_class($listener) . '::' . $method->getName() . ' has an illegal declaration');
            }

            $events = static::resolveEvents($method);
            $handler = static function (Event $event) use ($method, $listener): void {
                $method->setAccessible(true);
                $method->invoke($listener, $event);
            };

            foreach ($events as $event) {
                $pluginManager->registerEvent($event, $handler, $eventHandler->priority, $plugin, $eventHandler->handleCancelled);
            }
        }
    }

    private static function resolveEventHandler(\ReflectionMethod $method): ?EventHandler {
        $attributes = $method->getAttributes(EventHandler::class);
        if (empty($attributes)) {
            return null;
        }
        return $attributes[0]->newInstance();
    }

    private static function resolveEvents(\ReflectionMethod $method): array {
        if ($method->getNumberOfRequiredParameters() !== 1) {
            throw new PluginException('The event-handler method must have only one parameter');
        }

        $parameterType = $method->getParameters()[0]->getType();

        if ($parameterType === null) {
            throw new PluginException('The parameter of event-handler method must have a type');
        }

        $events = [];

        if ($parameterType instanceof \ReflectionNamedType) {
            $events[] = $parameterType->getName();
        } else if ($parameterType instanceof \ReflectionUnionType) {
            foreach ($parameterType->getTypes() as $type) {
                $events[] = $type->getName();
            }
        } else {
            throw new PluginException('The parameter of event-handler method has an illegal type');
        }

        return $events;
    }
}