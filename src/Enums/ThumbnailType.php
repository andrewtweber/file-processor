<?php

namespace FileProcessor\Enums;

enum ThumbnailType
{
    // Fit: keep aspect ratio, long side will match given dimension
    case Fit;

    // Fill: keep aspect ratio, crop to fit both dimensions
    case Fill;

    /**
     * These correspond to methods on the simpleimage class
     *
     * @return string
     */
    public function simpleImageMethod(): string
    {
        return match ($this) {
            self::Fit => 'bestFit',
            self::Fill => 'thumbnail',
        };
    }
}
