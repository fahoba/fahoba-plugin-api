<?php

declare(strict_types=1);

namespace Fahoba\PluginApi\Photos;

use Fahoba\PluginApi\Connections\ConnectedAccount;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * The frame's side of a photo service: a source only lists and downloads.
 *
 * The listing is kept for LIST_TTL seconds per connection — a kiosk asks for
 * a batch every few minutes, and a library does not change by the minute.
 * A failing service costs its pictures for a minute, not the frame. Pictures
 * go through the core's PhotoCache: fetched once, redrawn, served locally.
 * Only an ID from the current listing is fetched; anything else a device
 * sends is a guess.
 *
 * IDs end up in a path segment: a source whose own IDs may hold a slash
 * passes them through encodeId()/decodeId().
 */
abstract class RemotePhotoSource implements ListsPhotos, PhotoSource
{
    /** Seconds a listing is kept. */
    protected const LIST_TTL = 600;

    /** Seconds a failed listing is kept as empty, so a dead service is not asked on every request. */
    protected const FAILED_TTL = 60;

    /**
     * Every picture the connection shows, with the day it was taken where known.
     *
     * @return array<int, Photo>
     */
    abstract protected function listing(ConnectedAccount $account): array;

    /** The picture's bytes, or null when the service does not have it. */
    abstract protected function download(ConnectedAccount $account, string $id): ?string;

    public function isConfigured(?ConnectedAccount $account): bool
    {
        return $account !== null && $account->isActive();
    }

    /** @return array<int, Photo> */
    public function random(?ConnectedAccount $account, int $count): array
    {
        $photos = $this->all($account);

        shuffle($photos);

        return array_slice($photos, 0, $count);
    }

    /** @return array<int, Photo> */
    public function all(?ConnectedAccount $account): array
    {
        if (! $this->isConfigured($account)) {
            return [];
        }

        /** @var ConnectedAccount $account */
        $key = $this->listingKey($account);
        $kept = Cache::get($key);

        if (is_array($kept)) {
            return $kept;
        }

        try {
            $photos = array_values($this->listing($account));
            Cache::put($key, $photos, static::LIST_TTL);
        } catch (Throwable) {
            $photos = [];
            Cache::put($key, $photos, static::FAILED_TTL);
        }

        return $photos;
    }

    public function file(?ConnectedAccount $account, string $id): ?PhotoFile
    {
        if ($account === null || ! $this->listed($account, $id)) {
            return null;
        }

        return app(PhotoCache::class)->remember($account, $id, fn (): ?string => $this->download($account, $id));
    }

    /** The listing changes with what was entered, so a new album shows at once. */
    protected function listingKey(ConnectedAccount $account): string
    {
        return sprintf(
            'photo-listing:%d:%s',
            $account->accountId(),
            md5((string) json_encode([$account->credentials(), $account->settings()])),
        );
    }

    protected static function encodeId(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    protected static function decodeId(string $id): ?string
    {
        $raw = base64_decode(strtr($id, '-_', '+/'), true);

        return $raw === false ? null : $raw;
    }

    private function listed(ConnectedAccount $account, string $id): bool
    {
        foreach ($this->all($account) as $photo) {
            if ($photo->id === $id) {
                return true;
            }
        }

        return false;
    }
}
