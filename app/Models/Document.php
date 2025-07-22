<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = ['url', 'relate_to_id', 'relate_to_type'];

    /**
     * Get the parent model (Office, User, etc.)
     */
    public function relateTo(): MorphTo
    {
        return $this->morphTo();
    }
}
