# AdvancedEvents

This is a [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) plugin that allows you to comfortably register events using a new piece of PHP 8 power — [attributes](https://www.php.net/manual/language.attributes.overview.php).

*Inspired by [qPexLegendary's CoolEventListener](https://github.com/qPexLegendary/CoolEventListener).*

## Examples

Below is a simple example of a plugin with a listener using the [AdvancedEvents](https://github.com/JuraSciix/AdvancedEvents).

```php
<?php

use JuraSciix\AdvancedEvents\Attribute\EventHandler;
use JuraSciix\AdvancedEvents\AdvancedEvents;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

// There is no need to implement the \pocketmine\event\Listener interface.
// Implementation makes sense only if you need to inform the code reader that class handles events.
class Example extends PluginBase /*implements Listener*/ {

    protected function onEnable(): void {
        // Register this class as a listener on behalf of this plugin.
        AdvancedEvents::registerEvents($this, $this);
    }

    // The method that handles certain events is detected by the EventHandler attribute, unlike PM-MP.
    // In other words, this attribute is required for each method that handles events.
    #[EventHandler(priority: EventPriority::LOWEST)]
    private function onPlayerJoin(PlayerJoinEvent $event): void {
        // Due to the LOWEST priority, we are not required to perform various checks here.
        $event->getPlayer()->sendMessage("Welcome!");
    }
    
    // You can combine the handling of several events at once
    #[EventHandler]
    private function onBlockPlaceOrBreak(BlockPlaceEvent|BlockBreakEvent $event): void {
        $event->cancel();
        $event->getPlayer()->sendMessage("Pls don't do this.");
    }
}
```
