<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 

class Message extends Model
{
    use HasFactory, UUID;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'from_id',
        'patient_id',
        'body'
    ];

    protected $casts = [
        'body' => 'array',
    ];

    public function patient() {
        return $this->belongsTo(PatientProfile::class);
    }

    public function from() {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function read() {
        return $this->belongsToMany(User::class, 'messages_users', 'message_id', 'user_id')->withPivot('created_at')->select('user_id');
    }

    public function getIsReadAttribute() {
        $userProfile = auth()->guard('api')->user();
        return $this->read()->newPivotStatementForId($userProfile->id)->exists();
    }
}
