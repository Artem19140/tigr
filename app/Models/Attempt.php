<?php

namespace App\Models;

use App\Enums\AttemptStatus;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Database\Factories\AttemptFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{

    /** @use HasFactory<AttemptFactory> */
    use HasFactory;

    public const int MIN_TIME_FROM_START_TO_FINISH_MINUTES = 10;

    protected $fillable = [
        'foreign_national_id',
        'exam_id',
        'finished_at',
        'expired_at',
        'is_passed',
        'total_mark',
        'started_at',
        'annulled_reason',
        'annulled_by_id',
        'solved',
        'enrollment_id',
        'annulled_at',
        'checked_at',
        'last_activity_at',
        'speaking_finished_at',
        'speaking_started_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'finished_at' => 'datetime',
        'started_at' => 'datetime',
        'is_passed' => 'boolean',
        'annulled_at' => 'datetime',
        'checked_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'speaking_finished_at' => 'datetime',
        'speaking_started_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        if (! $this->expired_at) {
            return false;
        }

        return Carbon::now()->gte($this->expired_at);
    }

    public function finish(): void
    {
        $this->finished_at = Carbon::now();
    }

    public function annul(string $reason, int $annulledById): void
    {
        $this->annulled_reason = $reason;
        $this->annulled_by_id = $annulledById;
        $this->annulled_at = Carbon::now();
    }

    public function start(): void
    {
        $now = Carbon::now();
        $this->started_at = $now;
        $this->expired_at = $now->copy()->addMinutes($this->exam->type->duration);
        $this->last_activity_at = $now;
    }

    public function markAsChecked(): void
    {
        $this->checked_at = Carbon::now();
    }

    public function isStarted(): bool
    {
        return $this->started_at !== null;
    }

    public function isChecked(): bool
    {
        return $this->checked_at !== null;
    }

    public function isAnnulled(): bool
    {
        return $this->annulled_at !== null;
    }

    public function isFinished(): bool
    {
        return $this->finished_at !== null;
    }

    public function isPassed(): bool
    {
        if ($this->isAnnulled()) {
            return false;
        }

        return $this->is_passed;
    }

    public function foreignNational(): BelongsTo
    {
        return $this->belongsTo(ForeignNational::class, 'foreign_national_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class, 'attempt_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function canBeAutomaticallyFinalized(): bool
    {
        return ! $this->exam->type->need_human_check;
    }

    public function taskVariants(): BelongsToMany
    {
        return $this->belongsToMany(TaskVariant::class, 'attempt_answers');
    }

    public function scopeUnchecked(Builder $query): Builder
    {
        return $query
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->whereNull('checked_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNotNull('started_at')
            ->whereNull('annulled_at')
            ->whereNull('finished_at')
            ->whereNull('checked_at');
    }

    public function scopePassed(Builder $query):Builder
    {
        return $query
            ->where('is_passed', true)
            ->whereNull('annulled_at');
    }

    public function scopeFailed(Builder $query):Builder
    {
        return $query
            ->where('is_passed', false)
            ->orWhere(function(Builder $q){
                $q->where('is_passed', true)
                    ->whereNotNull('annulled_at');
            });
    }


    protected function timeZone(): Attribute
    {
        return Attribute::get(function () {
            return CenterData::timeZome();
        });
    }

    protected function startedAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->started_at->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function finishedAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->finished_at->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function speakingStartedAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->speaking_started_at->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function speakingFinishedAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->speaking_finished_at->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function annulledAtLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->annulled_at->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                $this->isAnnulled() => AttemptStatus::Annulled,
                $this->isFinished() => AttemptStatus::Finished,
                $this->isStarted() => AttemptStatus::Active,
                default => AttemptStatus::Pending,
            },
        );
    }

    public function canEditViolation():bool
    {
        return $this->finished_at === null;
    }

}
