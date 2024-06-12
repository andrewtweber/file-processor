<?php

namespace FileProcessor\Concerns;

use FileProcessor\Models\Photo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait FeaturedPhoto
 *
 * @package FileProcessor\Concerns
 *
 * @property int   $photo_id
 *
 * @property Photo $photo
 */
trait FeaturedPhoto
{
    // Recommend adding this to your model so that the featured photo is always eager-loaded:
    // public $with = ['photo'];

    /**
     * @return BelongsTo
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
