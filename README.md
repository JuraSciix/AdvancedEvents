# ModernEventAPI

## Introduction

This is a library for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) plugins that allows you to use a piece of the power of PHP 8 — Attributes.

We support PMMP 3.x and 4.x, as well as virions. Yes, you can use this as a virion (if you want).

## Integrating

Below is a simple example of a plugin with an event listener using the [ModernEventAPI](https://github.com/JuraSciix/ModernEventAPI).

```php
<?php

use JuraSciix\ModernEventAPI\Attribute\EventHandler;
use JuraSciix\ModernEventAPI\ModernEventAPI;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Example extends PluginBase implements Listener {

    protected function onEnable(): void {
        // Register this class as an event listener on behalf of the plugin of this example.
        ModernEventAPI::registerEvents($this, $this); // Instead of server plugin manager.
    }

    // The event handler is detected not by access modifiers or parameter signatures, but by the EventHandler attribute.
    // In other words, this attribute is required for event listeners.
    #[EventHandler(priority: EventPriority::LOWEST)]
    private function onPlayerJoin(PlayerJoinEvent $event): void {
        // Due to the LOWEST priority, we can not perform various checks here.
        $event->getPlayer()->sendMessage("Welcome!");
    }
}
```

## Avantile

Inspired by [qPexLegendary's CoolEventListener](https://github.com/qPexLegendary/CoolEventListener).