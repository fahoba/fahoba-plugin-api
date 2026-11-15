<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Messages;

/**
 * Sends a plugin's message to a household, bound by the core.
 *
 * Only for a sender the plugin offered (`offer('messages', …)`) and the
 * household's parents allowed. At most `PER_HOUR` messages per sender and
 * household reach anyone; the rest are dropped, so a plugin that loops
 * costs its own messages, not the family's peace.
 */
interface Messenger
{
    public const PER_HOUR = 10;

    /** Whether the household lets this sender through — ask before doing work for a message. */
    public function allowed(string $sender, int $householdId): bool;

    /**
     * Queues the message for its audience. False when the sender is not
     * offered or not allowed, the household is suspended, the
     * hourly limit is reached, or nobody is there to receive it.
     */
    public function send(string $sender, int $householdId, Message $message): bool;
}
