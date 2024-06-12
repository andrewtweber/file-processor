<?php

namespace FileProcessor;

use FileProcessor\Contracts\HasPhotosContract;
use FileProcessor\Models\File;
use FileProcessor\Models\Photo;
use FileProcessor\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Pluralizer;
use RuntimeException;

/**
 * Class FileProcessor
 *
 * @package FileProcessor
 */
class FileProcessor
{
    protected TempFile $file;

    /**
     * @param User|null $user
     */
    public function __construct(
        protected ?HasPhotosContract $entity = null,
        protected ?User $user = null
    ) {
    }

    /**
     * @param TempFile $file
     *
     * @return $this
     */
    public function fromTempFile(TempFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function fromUrl(string $url): self
    {
        $file = new TempFile($url, rename: true);
        $destination = fopen($file->fullPath(), 'w');

        $options = [
            CURLOPT_FILE           => $destination,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL            => $url,
            CURLOPT_FAILONERROR    => true, // HTTP code > 400 will throw curl error
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $return = curl_exec($ch);

        if ($return === false) {
            throw new \Exception(curl_error($ch));
        }

        fclose($destination);

        return $this->fromTempFile($file);
    }

    /**
     * @param mixed  $contents
     * @param string $filename
     *
     * @return $this
     */
    public function fromContents(mixed $contents, string $filename): self
    {
        $file = new TempFile($filename);
        Storage::put('uploads/' . $filename, $contents);

        return $this->fromTempFile($file);
    }

    /**
     * @param UploadedFile $original
     *
     * @return $this
     */
    public function fromFile(UploadedFile $original): self
    {
        if (! $original->isValid()) {
            throw new RuntimeException('Failed: ' . $original->getErrorMessage());
        }

        $file = new TempFile($original->getClientOriginalName(), rename: true);
        $file->original = $original;

        return $this->fromTempFile($file);
    }

    /**
     * Process thumbnails, upload, and return photo.
     *
     * @return Photo|File
     * @throws \Exception
     */
    public function run(): Photo|File
    {
        if (! isset($this->file)) {
            throw new RuntimeException("Please specify a path or URL");
        }

        if ($this->file->isImage()) {
            return $this->handlePhoto();
        }

        return $this->handleFile();
    }

    /**
     * @return Photo
     */
    protected function handlePhoto(): Photo
    {
        $thumbnail_ext = 'webp';
        $thumbnail_mimetype = 'image/webp';

        if (isset($this->file->original)) {
            $this->file->original->move($this->file->path, $this->file->filename);
        }

        [$width, $height] = $this->generateWithSimpleImage($thumbnail_ext, $thumbnail_mimetype);

        $folder = isset($this->entity)
            ? Pluralizer::plural($this->entity->getMorphClass())
            : 'photos';

        $max_weight = $this->entity?->photos()->max('weight') ?? 0;

        $photo = new Photo();
        $photo->user_id = $this->user->id ?? null;
        $photo->width = $width;
        $photo->height = $height;
        $photo->folder = $folder . '/' . date('Y/n');
        $photo->path = $this->file->filename;
        $photo->filename = $this->file->original?->getClientOriginalName();
        $photo->thumbnail_extension = $thumbnail_ext;
        $photo->weight = $max_weight + 1;

        if (isset($this->entity)) {
            $this->entity->photos()->save($photo);
        } else {
            $photo->save();
        }

        $photo->pushToS3();

        return $photo;
    }

    /**
     * @return File
     */
    protected function handleFile(): File
    {
        $folder = isset($this->entity)
            ? Pluralizer::plural($this->entity->getMorphClass())
            : 'files';

        $file = new File();
        $file->user_id = $this->user->id ?? null;
        $file->folder = $folder . '/' . date('Y/n');
        $file->path = $this->file->filename;
        $file->filename = $this->file->original?->getClientOriginalName();
        $file->mime_type = $this->file->original?->getClientMimeType();
        $file->size_in_bytes = $this->file->original?->getSize();
        $file->save();

        if (isset($this->entity)) {
            $this->entity->files()->save($file);
        } else {
            $file->save();
        }

        // This has to happen after `getMimeType` and `getSize` are called
        if (isset($this->file->original)) {
            $this->file->original->move($this->file->path, $this->file->filename);
        }

        $file->pushToS3();

        return $file;
    }
}
