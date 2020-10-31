<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'country',
        'city'

    ];
    /**
     * @return HasMany
     */

    public function users()
    {
        return $this->hasMany(User::class,'foreign_key');
    }
    /**
     * @return HasMany
     */

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class,'foreign_key');//->withTimestamps();
    }
}



