<?php

namespace JuraSciix\ModernEventAPI\Attribute;

use JetBrains\PhpStorm\ExpectedValues;
use pocketmine\event\EventPriority;

#[\Attribute(\Attribute::TARGET_METHOD)]
class EventHandler {

    public function __construct(
        #[ExpectedValues(valuesFromClass: EventPriority::class)]
        public int $priority = EventPriority::NORMAL,
        public bool $handleCancelled = false
    ) {}
}