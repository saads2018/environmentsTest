<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 

class Conversation extends Model
{
    use HasFactory, UUID;

    protected $table = "conversations";

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['last_message', 'other_participant'];

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];


    public function messages() {
        return $this->hasMany(PhysicianMessage::class)->with('from');
    }


    public function participants() {
        return $this->belongsToMany(User::class, 'conversation_participants', 'conversation_id', 'user_id');
    }


    public function getOtherParticipantAttribute() {
        $userProfile = auth()->guard('api')->user();
        $participants = $this->participants()->get();
        foreach ($participants as $p) {
            if($p->id == $userProfile->id) {
                continue;
            }
            $this->other_participant = $p;
            return $p;
        }
    }

    public function getNameAttribute() {
        $userProfile = auth()->guard('api')->user();
        $participants = $this->participants()->get();
        if(count($participants) == 2) {
            foreach ($participants as $p) {
                if($p->id == $userProfile->id) {
                    continue;
                }
                return $p->first_name . " " . $p->last_name;
            }
        }
        return $this->name;
        
    }

    public function getLastMessageAttribute() {
        return $this->messages()->orderBy('created_at', 'desc')->first();
    }

    public function read() {
        return $this->belongsToMany(User::class, 'messages_users', 'message_id', 'user_id')->withPivot('created_at')->select('user_id');
    }

}
