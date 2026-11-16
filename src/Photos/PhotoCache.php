<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Photos;

use Closure;
use Fahoba\PluginApi\Connections\ConnectedAccount;

/**
 * Pictures of a remote service as local files, bound by the core.
 *
 * A photo frame asks for the same pictures again and again; a source that
 * fetched each time would hammer the service and stall on every network gap.
 * The core fetches a picture once, redraws it (no metadata, at most the
 * frame's size) and keeps it per household and connection until it has not
 * been shown for a while, or the connection or household is gone.
 */
interface PhotoCache
{
    /**
     * The local file for a picture of this connection.
     *
     * `$fetch` runs only when the picture is not kept yet and returns the
     * image's bytes, or null when the service does not have it (any more).
     * Null when nothing came or the bytes are no picture.
     *
     * @param  string  $id  the source's own identifier of the picture
     * @param  Closure(): ?string  $fetch
     */
    public function remember(ConnectedAccount $account, string $id, Closure $fetch): ?PhotoFile;
}
