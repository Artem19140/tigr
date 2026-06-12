<?php

namespace App\Models;

use App\Models\Scopes\BelongsToCenter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ForeignNational extends Authenticatable
{
    use BelongsToCenter;
    use HasFactory, Notifiable;

    public function getRememberTokenName()
    {
        return null;
    }

    public const int STORAGE_TTL = 3;

    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'date_birth',
        'surname_latin',
        'name_latin',
        'patronymic_latin',
        'passport_number',
        'passport_series',
        'issued_by',
        'issued_date',
        'citizenship',
        'phone',
        'creator_id',
        'photo',
        'passport',
        'passport_translate',
        'document_type',
        'gender',
        'comment',
        'surname_normalized',
        'name_normalized',
        'patronymic_normalized',
        'passport_number_normalized',
        'passport_series_normalized',
        'address_reg',
        'center_id',
    ];

    protected $casts = [
        'exam_code_expired_at' => 'datetime',
        'date_birth' => 'date',
        'issued_date' => 'date',
    ];

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'enrollments')->withPivot('reg_number', 'has_payment');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'foreign_national_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function latestAttempt(): HasOne
    {
        return $this->attempts()->one()->latestOfMany();
    }

    protected function fullPassport(): Attribute
    {
        return Attribute::get(function () {
            return trim(
                ($this->passport_series ?? '').' '.($this->passport_number ?? '')
            );
        });
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            return trim(
                ($this->surname ?? '').' '.($this->name ?? '').' '.($this->patronymic ?? '')
            );
        });
    }

    protected function fullNameShort(): Attribute
    {
        return Attribute::get(function () {
            return trim(
                ($this->surname ?? '').' '.(mb_strtoupper(mb_substr($this->name, 0, 1)).'.' ?? '').' '.(mb_strtoupper(mb_substr($this->patronymic ?? '', 0, 1)))
            );
        });
    }

    protected function fullNameLatin(): Attribute
    {
        return Attribute::get(function () {
            return trim(
                ($this->surname_latin ?? '').' '.($this->name_latin ?? '').' '.($this->patronymic_latin ?? '')
            );
        });
    }

    protected function formattedPhone(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['phone'] ? $this->formatPhone($attributes['phone']) : null
        );
    }

    private function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // if (\strlen($cleaned) !== 10) {
        //     throw new \InvalidArgumentException("Некорректный формат телефона. Должно быть 10 цифр.");
        // }
        
        return '+7 (' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . '-' . substr($cleaned, 6, 2) . '-' . substr($cleaned, 8, 2);
    }
}
