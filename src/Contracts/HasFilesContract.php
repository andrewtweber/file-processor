<?php

namespace FileProcessor\Contracts;

use FileProcessor\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface HasFilesContract
 *
 * @package FileProcessor\Contracts
 *
 * @property Collection<File> $files
 */
interface HasFilesContract
{
    /**
     * @return MorphMany
     */
    public function files(): MorphMany;

    /**
     * @return string
     */
    public function getMorphClass();
}
