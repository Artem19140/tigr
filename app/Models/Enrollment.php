<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Number;

class Enrollment extends Model
{
    use HasFactory;

    public const int CLOSE_BEFORE_START_MINUTES = 10;

    protected $fillable = [
        'exam_id',
        'foreign_national_id',
        'has_payment',
        'reg_number',
        'status',
        'creator_id',
        'exam_code',
        'exam_code_expired_at',
        'cancelled_at',
        'rescheduled_at',
        'exam_code_used_at',
        'cancelled_by'
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

    public function foreignNational(): BelongsTo
    {
        return $this->belongsTo(ForeignNational::class);
    }

    public function attempt(): HasOne
    {
        return $this->hasOne(Attempt::class, 'enrollment_id')->latest();
    }

    public function scopeVisibleFor(Builder $query, Employee $employee): Builder
    {
        return $query->whereHas('exam', function ($q) use ($employee) {
            $q->visibleFor($employee);
        });
    }

    public function makeExamCodeUsed()
    {
        $this->update([
            'exam_code' => null,
            'exam_code_used_at' => Carbon::now()
        ]);
    }

    public function examCode(): string | null
    {
        return $this->exam_code;
    }

    public function amountInWords():string
    {
        return Number::spell($this->exam->amount(), 'ru');
    }

    public function isCancelled(): bool 
    {
        return $this->cancelled_at !== null;
    }

    public function hasPayment(): bool {
        return $this->has_payment;
    }
    
}
