<?php

namespace App\Models;

use App\Enums\EmployeeRole;
use App\Modules\Shared\CenterData;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Exam extends Model
{
    use HasFactory, Notifiable;
    public const int PROTOCOL_COMMENT_EDIT_TTL_DAYS = 7;

    protected $fillable = [
        'begin_time',
        'exam_type_id',
        'creator_id',
        'session_number',
        'session',
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

    public function duration(): int{
        return $this->type->duration;
    }

    public function tasksCount(): int{
        return $this->type->tasks_count;
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

    public function needHumanReview(): bool 
    {
        return  $this->type->need_human_review;
    }

    public function addressName(): string{
        return $this->address->address;
    }

    public function certificateName(): string{
        return $this->type->certificate_name;
    }

    public function level(): int{
        return $this->type->level;
    }

    public function shortName(): string{
        return $this->type->short_name;
    }

    public function fullName(): string{
        return $this->type->name;
    }

    public function amount(): int
    {
        return $this->type->amount;
    }

    public function timeZone(): string
    {
        return CenterData::timeZone();
 
    }

    public function beginTimeLocal(): Carbon
    {
        return $this->begin_time->copy()->setTimezone(
            $this->timeZone()
        );
    }

    public function endTimeLocal(): Carbon
    {
        return $this->end_time->copy()->setTimezone(
            $this->timeZone()
        );
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
            EmployeeRole::PlatformAdmin
        )){
            return $query;
        }

        return $query->whereHas('examiners', function (Builder $q) use ($employee) {
            $q->where('examiner_id', $employee->id);
        });
    }

    public function needPolling():bool
    {
        if($this->isCancelled()){
            return false;
        }

        if($this->begin_time->isFuture()){
            return false;
        }
        
        if(! $this->enrollments_exists){
            return false;
        }
    
        if(
            $this->enrollments_with_no_attempts_exists
                &&
            ! $this->codesTtlExpired()
        ){
            return true;
        }
        
        if(! $this->attempts_exists){
            return false;
        }

        return $this->active_attempts_exists;
    }

    public function codesTtlExpired():bool
    {
        return $this->begin_time->copy()->addMinutes(
            ExamSettings::codesTtlMinutes()
        )->isPast();
    }

    public function hasNoEnrollment(): bool
    {
        return ! $this->enrollments_exists;
    }

    public function hasNoAttempts(): bool
    {
        return ! $this->attempts_exists;
    }

    public function hasUnreviewdAttemtps(): bool
    {
        return $this->unreviewed_attempts_exists;
    }

    public function hasActiveAttempts(): bool
    {
        return $this->active_attempts_exists;
    }

    public function protocolCommentEditTtlPast(): bool
    {
        return $this->begin_time->addDays(
            self::PROTOCOL_COMMENT_EDIT_TTL_DAYS
        )
            ->copy()
            ->endOfDay()
            ->isPast();
    }
}