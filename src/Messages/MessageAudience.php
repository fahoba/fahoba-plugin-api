<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Messages;

/**
 * Who receives a message. `Parents` are the accounts that manage the
 * household; `Family` every account of it, children's phones included.
 */
enum MessageAudience: string
{
    case Parents = 'parents';
    case Family = 'family';
}
