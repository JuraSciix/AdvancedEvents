# ModernEventAPI

This is a library for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) plugin events that allows you to use a piece of the power of PHP 8 — [attributes](https://www.php.net/manual/language.attributes.overview.php).

*Inspired by [qPexLegendary's CoolEventListener](https://github.com/qPexLegendary/CoolEventListener).*

## Examples

Below is a simple example of a plugin with an event listener using the [ModernEventAPI](https://github.com/JuraSciix/ModernEventAPI).

```php
<?php

use JuraSciix\ModernEventAPI\Attribute\EventHandler;
use JuraSciix\ModernEventAPI\ModernEventAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Example extends PluginBase implements Listener {

    protected function onEnable(): void {
        // Register this class as an event listener on behalf of the plugin of this example.
        ModernEventAPI::registerEvents($this, $this); // Instead of server plugin manager.
    }

    // The event handling method is detected not by access modifiers or method signature, but by the EventHandler attribute.
    // In other words, this attribute is required for each event handling methods.
    #[EventHandler(priority: EventPriority::LOWEST)]
    private function onPlayerJoin(PlayerJoinEvent $event): void {
        // Due to the LOWEST priority, we can not perform various checks here.
        $event->getPlayer()->sendMessage("Welcome!");
    }
    
    // Yeah, you can combine events in one event handler method.
    #[EventHandler]
    private function onBlockPlaceOrBreak(BlockPlaceEvent|BlockBreakEvent $event): void {
        $event->cancel();
        $event->getPlayer()->sendMessage("Pls don't do this.");
    }
}
```
