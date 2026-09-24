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
        'review_mode'
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

    public function autoReview(): bool
    {
        if(! $this->review_mode){
            return $this->type->autoReview();
        }

        return $this->review_mode !== 'manual';
    }

    public function scopeManualReview(Builder $query)
    {
        return $query->whereIn('type', TaskType::manualReviewTypes())
            ->orWhere('review_mode', 'manual');
    }
}
