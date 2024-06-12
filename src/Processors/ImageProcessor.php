<?php

namespace FileProcessor\Processors;

use FileProcessor\Contracts\HasPhotosContract;
use FileProcessor\TempFile;
use FileProcessor\Thumbnail;
use FileProcessor\ThumbnailSize;
use Illuminate\Support\Collection;

abstract class ImageProcessor
{
    public function __construct(
        protected HasPhotosContract $entity,
        protected TempFile $file
    ) {
    }

    /**
     * @return Collection<Thumbnail>
     */
    public function generateThumbnails(): Collection
    {
        // Generate the thumbnails in order from largest to smallest.
        // The largest one will be used to generate the smaller ones, instead of using the original file
        // as the source each time, this is much more efficient e.g. if the original file is very large.
        $sizes = collect($this->entity->thumbnailSizes())
            ->sortByDesc(fn (ThumbnailSize $size) => $size->largestDimension());

        $thumbnails = collect([]);
        $largestThumbnail = null;

        foreach ($sizes as $size) {
            $result = $this->generateThumbnail($size, source: $largestThumbnail);
            $largestThumbnail ??= $result;

            // Results are keyed by label so you can easily find the one you need
            $thumbnails[$size->label] = $result;
        }

        return $thumbnails;
    }

    /**
     * @param ThumbnailSize $size
     * @param mixed $source
     *
     * @return Thumbnail
     */
    abstract public function generateThumbnail(ThumbnailSize $size, mixed $source = null): Thumbnail;
}
