<?php

namespace FileProcessor;

use FileProcessor\Enums\ThumbnailType;
use Mimey\MimeTypes;

class ThumbnailSize
{
    /**
     * @param string $label - an identifier for this size (e.g. "large", "small", "avatar")
     * @param int $width - width of image (maximum if type = Fit, exact if type = Fill)
     * @param int $height - height of image (maximum if type = Fit, exact if type = Fill)
     * @param int $quality - quality of image, from 1-100
     * @param string $suffix - a string that will be appended to the filename
     * @param string $mimetype - the type of image to save it as
     * @param ThumbnailType $type - thumbnail fit
     * @param bool $isPublic - whether the uploaded image should be publicly accessible or not
     */
    public function __construct(
        public string $label,
        public int $width,
        public int $height,
        public int $quality,
        public string $suffix,
        public string $mimetype,
        public ThumbnailType $type = ThumbnailType::Fill,
        public bool $isPublic = false
    ) {
    }

    public function extension(): string
    {
        $mimes = new MimeTypes;
        return $mimes->getExtension($this->mimetype);
    }

    public function largestDimension(): int
    {
        return max($this->width, $this->height);
    }
}
