<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'path',
        'creator_id',
        'documentable_type',
        'documentable_id',
        'original_name',
        'size_kb',
        'mime_type',
        'document_type',
        'center_id',
        'deleted_at'
    ];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator():BelongsTo
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function center():BelongsTo
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
