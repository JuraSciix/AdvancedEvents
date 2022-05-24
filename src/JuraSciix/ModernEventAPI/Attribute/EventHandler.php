<?php

namespace JuraSciix\ModernEventAPI\Attribute;

use pocketmine\event\EventPriority;

#[\Attribute(\Attribute::TARGET_METHOD)]
class EventHandler {

    public int $priority;

    public bool $ignoreCancelled;

    public function __construct(int $priority = EventPriority::NORMAL, bool $ignoreCancelled = false) {
        self::ensureLegalPriority($priority);
        $this->priority = $priority;
        $this->ignoreCancelled = $ignoreCancelled;
    }

    private static function ensureLegalPriority(int $priority): void {
        if (!in_array($priority, EventPriority::ALL)) {
            throw new \InvalidArgumentException('illegal event priority');
        }
    }
}