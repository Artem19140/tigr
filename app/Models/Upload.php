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
        'original_name',
        'path',
        'uuid'
    ];

    public function chunksPath(): string
    {
        return "uploads/$this->uuid";
    }

    public function allChunksRecieved():bool
    {
        return $this->total_chunks === $this->uploaded_chunks;
    }
}
