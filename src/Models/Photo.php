<?php

namespace FileProcessor\Models;

use FileProcessor\Concerns\ImageFile;
use FileProcessor\Contracts\ImageFileContract;
use FileProcessor\Contracts\HasPhotosContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Photo
 *
 * @package FileProcessor\Models
 *
 * @property int                    $id
 * @property int                    $user_id
 * @property string|null            $entity_type
 * @property string|null            $entity_id
 * @property string                 $folder
 * @property string                 $path
 * @property int                    $width
 * @property int                    $height
 * @property string                 $thumbnail_extension
 * @property int                    $weight
 * @property ?string                $filename - original file name
 * @property Carbon                 $created_at
 * @property Carbon                 $updated_at
 * @property Carbon                 $deleted_at
 *
 * @property User                   $user
 * @property HasPhotosContract|null $entity
 */
class Photo extends Model implements ImageFileContract
{
    use ImageFile, SoftDeletes;

    protected $table = 'photos';

    protected $guarded = ['id'];

    protected $appends = [
        'original',
        'large',
        'medium',
        'small',
        'tiny',
    ];

    /**
     * Available sizes
     */
    protected $sizes = ['tn', 'sm', 'md', 'lg'];

    /**
     * User who uploaded photo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->getTable() . '/' . $this->folder;
    }

    /**
     * @return string
     */
    public function extension(): string
    {
        return $this->thumbnail_extension;
    }

    /**
     * @return string
     */
    public function mimeType(): string
    {
        return $this->thumbnail_extension === 'jpg' ? 'image/jpeg' : 'image/webp';
    }
}
