<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Dashboard;

/**
 * One tile of a type on some dashboard, as background work sees it: the
 * household it belongs to, that household's zone and language, and the
 * tile's settings with secrets decrypted.
 */
final readonly class PlacedTile
{
    /** @param  array<string, mixed>  $settings */
    public function __construct(
        public int $householdId,
        public string $timezone,
        public string $locale,
        public array $settings,
    ) {}
}
