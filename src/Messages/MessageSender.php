<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Messages;

/**
 * A plugin that wants to send messages to a family:
 * `offer('messages', 'vendor/name', Class)`.
 *
 * Offering is not permission. Parents tick each sender under Household →
 * Messages from plugins; until then `Messenger::send()` delivers nothing.
 * Label and description are what they read before deciding — say what
 * arrives and how often.
 */
interface MessageSender
{
    public function label(): string;

    public function description(): string;
}
