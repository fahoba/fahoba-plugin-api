<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Messages;

use InvalidArgumentException;

/**
 * One message: it lands in each recipient's bell and, with `push`, on
 * their phones. Write it in the household's language
 * (`CurrentHousehold::locale()`) — it is stored as written.
 *
 * `link` is where a tap leads: `https://…` or a path on this server
 * (`/?area=tasks`). `tag` groups pushes: a new push with the same tag
 * replaces the last one on the phone instead of piling up.
 */
final readonly class Message
{
    public const MAX_TITLE = 120;

    public const MAX_BODY = 500;

    public function __construct(
        public string $title,
        public ?string $body = null,
        public ?string $link = null,
        public MessageAudience $audience = MessageAudience::Parents,
        public bool $push = true,
        public ?string $tag = null,
    ) {
        if (trim($title) === '') {
            throw new InvalidArgumentException('A message needs a title.');
        }

        if ($link !== null && ! str_starts_with($link, 'https://') && ! (str_starts_with($link, '/') && ! str_starts_with($link, '//'))) {
            throw new InvalidArgumentException('A message links to https:// or to a path on this server.');
        }

        if ($tag !== null && preg_match('/^[a-z0-9-]{1,40}$/', $tag) !== 1) {
            throw new InvalidArgumentException('A tag is up to 40 lower-case letters, digits and dashes.');
        }
    }
}
