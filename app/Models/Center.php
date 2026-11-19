<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Center extends Model
{
    use HasFactory;
    use LogsActivity;
    public const string  CACHE_KEY = 'center.data';

    protected $fillable = [
        'name',
        'director_fio',
        'certificates_issue_address',
        'ogrn',
        'inn',
        'registered_address',
        'name_genitive',
        'time_zone',
        'commission_chairman',
        'short_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
