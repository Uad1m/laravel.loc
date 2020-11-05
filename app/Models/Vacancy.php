<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vacancy_name',
        'workers_amount',
        'workers_apply_amount',
        'organization_id',
        'user_id',
        'salary'
    ];

    /**
     * @return HasMany
     */
/*
    public function users()
    {
        return $this->hasMany(Organization::class,'foreign_key')->withTimestamps();
    }
    */
    /**
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class,'organization_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class,'user_id');
    }


}
