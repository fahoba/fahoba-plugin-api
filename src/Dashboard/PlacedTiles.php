<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Dashboard;

/**
 * Where a widget was placed, across every household — for a scheduled
 * command that acts on what families set up in their tiles (a reminder
 * the evening before). Bound by the core.
 *
 * Suspended households are left out. A household with the tile on the
 * wall and on two phones appears three times; group by what matters.
 */
interface PlacedTiles
{
    /** @return list<PlacedTile> */
    public function of(string $type): array;
}
