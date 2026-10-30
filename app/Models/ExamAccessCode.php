<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAccessCode extends Model
{
    public const string INITIAL_TYPE = 'initial';
    public const string RECOVERY_TYPE = 'recovery';
    public const int RECOVERY_CODE_TTL_MINUTES = 5;
    protected $fillable = [
        'code',
        'code_snapshot',
        'type',
        'recovery_reason',
        'generated_by',
        'expires_at',
        'used_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    public function isExpired(): bool {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool {
        return $this->used_at !== null;
    }

    public function makeUsed(): void {
        $this->update([
            'used_at' => now(),
            'code' => null
        ]);
    }
    
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
    
}
