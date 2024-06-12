<?php

namespace FileProcessor\Concerns;

use Illuminate\Support\HtmlString;

/**
 * Trait ImageFile
 *
 * @package FileProcessor\Concerns
 */
trait ImageFile
{
    /**
     * Retrieve thumbnail URL of given size.
     *
     * @param string $size
     *
     * @return string
     */
    protected function getUrl($size): string
    {
        list($name, $ext) = Helpers::parseFilename($this->path);

        $path = 'https://' . config('app.cdn') . '/' . $this->path() . '/';

        if ($name === 'default') {
            return "{$path}{$name}.{$ext}";
        }

        if (in_array($size, $this->sizes)) {
            $name .= '_' . $size;
            $ext  = $this->extension();
        }

        return "{$path}{$name}.{$ext}";
    }

    /**
     * @inheritDoc
     */
    public function secureUrl(): string
    {
        return str_replace('http://', 'https://', $this->large);
    }

    /**
     * @inheritDoc
     */
    public function insecureUrl(): string
    {
        return str_replace('https://', 'http://', $this->large);
    }

    /**
     * Upload photo and all scaled versions to S3.
     */
    public function pushToS3(bool $include_original = true)
    {
        [$name, $ext] = Helpers::parseFilename($this->path);

        $local = storage_path('app/uploads') . '/';
        $remote = $this->path() . '/';

        if ($include_original) {
            Helpers::pushToS3("{$local}{$name}.{$ext}", "{$remote}{$name}.{$ext}", public: false);
        } else {
            unlink("{$local}{$name}.{$ext}");
        }

        $ext = $this->extension();

        foreach ($this->sizes as $size) {
            Helpers::pushToS3("{$local}{$name}_{$size}.{$ext}", "{$remote}{$name}_{$size}.{$ext}", public: true);
        }
    }

    /**
     * @param int  $long_side
     * @param bool $array
     *
     * @return mixed
     */
    public function dimensions(int $long_side, bool $array = false): mixed
    {
        if (! $this->photo) {
            return $array ? [] : null;
        }

        // Dimensions of large image
        $width = $this->photo->width;
        $height = $this->photo->height;

        if ($width === $height) {
            // Square is easy
            $width = $height = $long_side;
        } elseif ($width > $height) {
            // Wide image
            $proportion = $long_side / $width;
            $height = (int)($proportion * $height);
            $width = $long_side;
        } else {
            // Tall image
            $proportion = $long_side / $height;
            $width = (int)($proportion * $width);
            $height = $long_side;
        }

        if ($array) {
            return [
                'width'  => $width,
                'height' => $height,
            ];
        }

        return new HtmlString(' width="' . $width . '" height="' . $height . '" ');
    }
}
