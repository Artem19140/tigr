<?php

namespace App\Models;

use App\Enums\EmployeeRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => EmployeeRole::class,
    ];

    public static function findByEnum(EmployeeRole $role): Role|\stdClass
    {
        return self::where('name', $role)->firstOrFail();
    }
}
