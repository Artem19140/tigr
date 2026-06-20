<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'is_active',
        'has_speaking_tasks',
        'tasks_count',
        'need_human_check',
        'min_mark',
        'protocol_name',
        'amount_in_words'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_speaking_tasks' => 'boolean',
        'need_human_check' => 'boolean',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'exam_type_id');
    }

    public static function cached()
    {
        return Cache::rememberForever('exam_types', function () {
            return self::all();
        });
    }
}
