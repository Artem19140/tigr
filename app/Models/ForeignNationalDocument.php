<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForeignNationalDocument extends Model
{ 
    /** @use HasFactory<\Database\Factories\ForeignNationalDocumentsFactory> */
    use HasFactory;

    public const int MAX_SIZE_MB = 20;

    public static function allowedMimes():array
    {
        return ['pdf'];
    }

    protected $fillable = [
        'path',
        'creator_id',
        'foreign_national_id',
        'original_name',
        'size_bytes',
        'mime_type',
        'document_type',
        'disk',
        'deleted_at'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function foreignNational(): BelongsTo
    {
        return $this->belongsTo(ForeignNational::class);
    }

}
