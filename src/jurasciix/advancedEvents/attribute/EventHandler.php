<?php

declare(strict_types=1);

namespace jurasciix\advancedEvents\attribute;

use JetBrains\PhpStorm\ExpectedValues;
use pocketmine\event\EventPriority;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class EventHandler {

    public function __construct(
        #[ExpectedValues(valuesFromClass: EventPriority::class)]
        public int $priority = EventPriority::NORMAL,
        public bool $handleCancelled = false
    ) {}
}