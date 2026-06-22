<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'total_chunks',
        'uploaded_chunks',
        'status',
        'center_id',
        'mime_type',
        'original_name',
        'path',
        'uuid'
    ];
}
