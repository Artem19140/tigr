<?php

namespace App\Models;

use App\Modules\Shared\CenterData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    /** @use HasFactory<\Database\Factories\ViolationFactory> */
    use HasFactory;
    protected $fillable = [
        'attempt_id',
        'exam_id',
        'comment',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }

    protected function createdAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->created_at->copy()->setTimezone(CenterData::timeZome());
        });
    }
}
