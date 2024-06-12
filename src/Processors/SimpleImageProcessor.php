<?php

namespace FileProcessor\Processors;

use claviska\SimpleImage;
use FileProcessor\Thumbnail;
use FileProcessor\ThumbnailSize;

class SimpleImageProcessor extends ImageProcessor
{
    /**
     * @param ThumbnailSize $size
     * @param mixed $source
     *
     * @return Thumbnail
     */
    public function generateThumbnail(ThumbnailSize $size, mixed $source = null): Thumbnail
    {
        // string $thumbnail_ext, string $thumbnail_mimetype
        $base = $this->file->basename;

        // Always generate a "large" version. This is what the thumbnails will be generated from.
        // This is more efficient than generating them from the original file.
        if (! $source) {
            $source = (new SimpleImage())
                ->fromFile($this->file->fullPath())
                ->autoOrient();
        } else {
            $source = clone $source;
        }

        $method = $size->type->simpleImageMethod();
        $path = "{$this->file->path}/{$base}_{$size->suffix}.{$size->extension()}";

        $image = $source
            ->$method($size->width, $size->height)
            ->toFile($path, $size->mimetype, $size->quality);

        return new Thumbnail($size->label, $image->getWidth(), $image->getHeight(), $path);
    }
}
