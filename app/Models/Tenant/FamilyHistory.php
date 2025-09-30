<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

use App\Traits\UUID;
use App\Enums\EducationCode;

class FamilyHistory extends Model
{
	use HasFactory, UUID;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    	'name',
        'codes'
    ];

    protected $casts = [
        'codes' => AsEnumCollection::class.':'.EducationCode::class,
    ];

}