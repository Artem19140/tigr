<?php

namespace App\Models;

use App\Models\Scopes\BelongsToCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use BelongsToCenter;
    use HasFactory;

    public const int CLOSE_BEFORE_START_MINUTES = 10;

    protected $fillable = [
        'exam_id',
        'foreign_national_id',
        'has_payment',
        'reg_number',
        'status',
        'creator_id',
        'center_id',
        'exam_code',
        'exam_code_expired_at',
        'cancelled_at',
        'rescheduled_at',
        'exam_code_used_at',
    ];

    protected $casts = [
        'has_payment' => 'boolean',
        'exam_code_expired_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'exam_code_used_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function foreignNational(): BelongsTo
    {
        return $this->belongsTo(ForeignNational::class);
    }

    public function attempt(): HasOne
    {
        return $this->hasOne(Attempt::class, 'enrollment_id');
    }

    public function scopeVisibleFor(Builder $query, Employee $employee): Builder
    {
        return $query->whereHas('exam', function ($q) use ($employee) {
            $q->visibleFor($employee);
        });
    }

    protected function timeZone(): Attribute
    {
        return Attribute::get(function () {
            return $this->center->time_zone;
        });
    }
}
