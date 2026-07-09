<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{

    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $fillable = [
        'is_active',
        'address',
        'capacity',
        'creator_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer'
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'address_id');
    }

}
