<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'name',
        'director_fio',
        'certificates_issue_address',
        'ogrn',
        'inn',
        'address',
        'name_genitive',
        'time_zone',
        'commission_chairman',
        'short_name',
    ];
}
