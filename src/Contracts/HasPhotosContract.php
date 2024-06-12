<?php

namespace FileProcessor\Contracts;

use FileProcessor\Models\Photo;
use FileProcessor\ThumbnailSize;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface HasPhotosContract
 *
 * @package FileProcessor\Contracts
 *
 * @property Collection<Photo> $photos
 */
interface HasPhotosContract
{
    /**
     * @return array<ThumbnailSize>
     */
    public function thumbnailSizes(): array;

    /**
     * @return MorphMany
     */
    public function photos(): MorphMany;

    /**
     * @return string
     */
    public function getMorphClass();
}
