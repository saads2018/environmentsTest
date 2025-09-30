<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 

class PhysicianMessage extends Model
{
    use HasFactory, UUID;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'from_id',
        'conversation_id',
        'body',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_read'];

    protected $casts = [
        'body' => 'array',
    ];


    public function from() {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function conversation() {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function read() {
        return $this->belongsToMany(User::class, 'messages_users', 'message_id', 'user_id')->withPivot('created_at')->select('user_id');
    }

    public function getIsReadAttribute() {
        $userProfile = auth()->guard('api')->user();
        return $this->read()->newPivotStatementForId($userProfile->id)->exists();
    }
}
