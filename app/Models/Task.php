<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'type',
        'subblock_id',
        'order',
        'mark',
        'checking_mode'
    ];

    protected $casts = [
        'type' => TaskType::class
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(TaskVariant::class, 'task_id');
    }

    public function subblock(): BelongsTo
    {
        return $this->belongsTo(Subblock::class, 'subblock_id');
    }

    public function autoCheck(): bool
    {
        if(! $this->checking_mode){
            return $this->type->autoCheck();
        }

        return $this->checking_mode !== 'manual';
    }

    public function scopeManualReview(Builder $query)
    {
        return $query->whereIn('type', TaskType::manualReviewTypes())
            ->orWhere('checking_mode', 'manual');
    }
}
