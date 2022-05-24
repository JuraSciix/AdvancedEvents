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
        $pluginManager = Server::getInstance()->getPluginManager();

        foreach ((new \ReflectionClass($listener))->getMethods() as $method) {
            $eventHandlerAttributes = $method->getAttributes(EventHandler::class);

            // Nukkit's concept.
            if (empty($eventHandlerAttributes)) {
                continue;
            }

            /** @var $eventHandler EventHandler */
            $eventHandler = $eventHandlerAttributes[0]->newInstance();

            if ($method->getNumberOfParameters() !== 1) {
                throw new PluginException('A method attributed with ' . EventHandler::class . ' must have only one parameter');
            }

            $handler = static function (Event $event) use ($method, $listener): void {
                $method->setAccessible(true);
                $method->invoke($listener, $event);
            };

            $pluginManager->registerEvent($method->getParameters()[0], $handler, $eventHandler->priority, $plugin, $eventHandler->ignoreCancelled);
        }
    }
}