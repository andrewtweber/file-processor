<?php

namespace FileProcessor;

use Illuminate\Http\UploadedFile;

/**
 * Class TempFile
 *
 * @package FileProcessor
 */
class TempFile
{
    public readonly string $path;

    public readonly string $filename;

    public readonly string $basename;

    public readonly string $extension;

    public ?UploadedFile $original = null;

    /**
     * @param string $filename
     * @param bool   $rename
     */
    public function __construct(string $filename, bool $rename = false)
    {
        $this->path = storage_path('app/uploads');

        [$base, $ext] = Helpers::parseFilename($filename, $rename);
        $this->filename = $base . '.' . $ext;
        $this->basename = $base;
        $this->extension = $ext;
    }

    /**
     * @return string
     */
    public function fullPath(): string
    {
        return $this->path . '/' . $this->filename;
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->extension, config('file-processor.extensions.images'));
    }
}
