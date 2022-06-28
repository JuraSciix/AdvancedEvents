<?php

namespace JuraSciix\AdvancedEvents\Attribute;

use JetBrains\PhpStorm\ExpectedValues;
use pocketmine\event\EventPriority;

/**
 * An attribute for indicating the method that handles events.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class EventHandler {

    /**
     * @param int $priority Event handling priority.
     * @param bool $handleCancelled Still handle the event even if it was canceled.
     */
    public function __construct(
        #[ExpectedValues(valuesFromClass: EventPriority::class)]
        public int $priority = EventPriority::NORMAL,
        public bool $handleCancelled = false
    ) {}
}