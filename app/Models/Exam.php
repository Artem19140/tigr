<?php

namespace App\Models;

use App\Enums\EmployeeRole;
use App\Models\Scopes\BelongsToCenter;
use App\Support\TimePresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;

class Exam extends Model
{
    use BelongsToCenter;
    use HasFactory, Notifiable;

    public const CREATE_AVAILABLE_BEFORE_HOURS = 3;

    public const CODES_LENGTH = 6;

    public const CODES_TTL_AFTER_BEGIN_MINUTES = 45;

    protected $fillable = [
        'begin_time',
        'exam_type_id',
        'creator_id',
        'session_number',
        'capacity',
        'comment',
        'group',
        'address_id',
        'date',
        'cancelled_reason',
        'center_id',
        'end_time',
        'protocol_comment',
        'cancelled_at',
    ];

    protected $casts = [
        'end_time' => 'datetime',
        'begin_time' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function examiners(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'exam_examiner', 'exam_id', 'examiner_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function scopeExaminer(Builder $query, Employee $employee): Builder
    {
        if($employee->isPlatformAdmin()){
            return $query;
        }
        return $query->whereHas('examiners', function (Builder $q) use ($employee) {
            $q->where('examiner_id', $employee->id);
        });
    }

    public function foreignNationals(): BelongsToMany
    {
        return $this->belongsToMany(ForeignNational::class, 'enrollments')->withPivot('reg_number', 'has_payment');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'exam_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function isFinished(): bool
    {
        return $this->end_time->isPast();
    }

    public function isGoing(): bool
    {
        return $this->begin_time->isPast() && $this->end_time->isFuture();
    }

    public function isPending(): bool
    {
        return $this->begin_time->isFuture();
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    protected function duration(): Attribute
    {
        return Attribute::get(function () {
            return $this->type->duration;
        });
    }

    protected function addressName(): Attribute
    {
        return Attribute::get(function () {
            return $this->address->address;
        });
    }

    public function hasSpeaking(): bool
    {
        return $this->type->has_speaking_tasks;
    }

    protected function timeZone(): Attribute
    {
        return Attribute::get(function () {
            return $this->center->time_zone;
        });
    }

    protected function beginTimeLocal(): Attribute
    {
        return Attribute::get(function () {
            return TimePresenter::forCenter($this->begin_time, $this->center);
        });
    }

    protected function endTimeLocal(): Attribute
    {
        return Attribute::get(function () {
            return TimePresenter::forCenter($this->end_time, $this->center);
        });
    }

    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('cancelled_at', null);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('cancelled_at', '<>', null);
    }

    protected function name(): Attribute
    {
        return Attribute::get(function () {
            return $this->type->name;
        });
    }

    protected function shortName(): Attribute
    {
        return Attribute::get(function () {
            return $this->type->short_name;
        });
    }

    public function scopeVisibleFor(
        Builder $query,
        Employee $employee
    ): Builder {
        if($employee->hasAnyRole(
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::Scheduler,
            EmployeeRole::PlatformAdmin
        )){
            return $query;
        }

        return $query->whereHas('examiners', function (Builder $q) use ($employee) {
            $q->where('examiner_id', $employee->id);
        });
    }
}