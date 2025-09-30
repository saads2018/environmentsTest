<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID;

class QuizOrder extends Model
{
    use HasFactory, UUID;

    protected $table = "quiz_order";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'quiz_id',
        'order',
    	'code',
    ];

}
