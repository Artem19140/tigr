<?php

namespace App\Models;

use App\Enums\EmployeeRole;
use App\Modules\Shared\CenterData;
use App\Modules\Shared\ExamSettings;
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
    use HasFactory, Notifiable;

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

    public function isPending(): bool
    {
        return $this->begin_time->isFuture();
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function hasSpeaking(): bool
    {
        return $this->type->has_speaking_tasks;
    }

    protected function timeZone(): Attribute
    {
        return Attribute::get(function () {
            return CenterData::timeZome();
        });
    }

    protected function beginTimeLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->begin_time->copy()->setTimezone(CenterData::timeZome());
        });
    }

    protected function endTimeLocal(): Attribute
    {
        return Attribute::get(function () {
            return $this->end_time->copy()->setTimezone(CenterData::timeZome());
        });
    }

    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('cancelled_at', null);
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

    public function isGoing():bool
    {
        if($this->begin_time->isFuture()){
            return false;
        }
        
        if(! $this->enrollments_exists){
            return false;
        }
    
        if(! $this->codesTtlExpired()){
            return true;
        }
        
        if(! $this->attempts_exists){
            return false;
        }

        return $this->active_attempts_exists;
    }

    public function codesTtlExpired():bool
    {
        return $this->begin_time->copy()->addMinutes(ExamSettings::codesTtlMinutes())->isPast();
    }

    public function loadState(){
        $this->loadExists([
            'attempts as unchecked_attempts_exists' => function ($query) {
                $query->unchecked();
            },
            'attempts as active_attempts_exists' => function ($query) {
                $query->active();
            },
            'attempts',
            'enrollments'
        ]);
    }
}