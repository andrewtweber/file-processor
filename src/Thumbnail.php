<?php

namespace FileProcessor;

class Thumbnail
{
    public function __construct(
        public string $label,
        public int $width,
        public int $height,
        public string $path,
    ) {
    }
}
