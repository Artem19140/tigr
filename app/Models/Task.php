<?php

namespace App\Models;

use App\Enums\TaskType;
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
        'settings'
    ];

    protected $casts = [
        'type' => TaskType::class,
        'settings' => 'array'
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
        if(! $this->settings){
            return $this->type->autoCheck();
        }
        
        return $this->settings['checking_mode'] !== 'manual';
    }
}
