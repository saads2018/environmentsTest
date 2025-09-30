<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;
use Illuminate\Database\Eloquent\Builder;

use App\Http\Requests\Message\IndexRequest;
use App\Http\Requests\Message\StoreRequest;

use App\Http\Requests\PhysicianMessage\StoreRequest as PhysicianStoreRequest;
use App\Http\Requests\PhysicianMessage\IndexRequest as PhysicianIndexRequest;

use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageCollection;

use App\Models\Tenant\Message;
use App\Models\Tenant\PhysicianMessage;
use App\Models\Tenant\Conversation;

use App\Http\Resources\PhysicianMessageResource;
use App\Http\Resources\ConversationCollection;
use App\Http\Resources\PhysicianMessageCollection;


use App\Models\Tenant\PatientProfile;
use App\Models\Tenant\PhysicianProfile;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\Message\NewPatient as NewPatientMessage;
use App\Mail\Message\NewPhysician as NewPhysicianMessage;
use App\Mail\PhysicianMessage\NewMessage as NewMessage;

class MessagesController extends ApiController
{

	protected Message $message;

	public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function index(IndexRequest $request)
    {
        //If patient is request, return list of messages for himself
        $user = auth()->guard('api')->user();
        if($user->isPatient) {
            $patientId = $user->profile->id;
            return $this->listForPatient($patientId, $request->limit, false);
		}

        //returns list of patients with only latest messages
        $collection = Message::with(['patient'])
        ->whereIn('created_at', function ($query) {
                $query->select(\DB::raw("MAX(created_at)"))
                    ->from('messages')
                    ->groupBy('patient_id');
            })
        ->orderBy('created_at', 'desc');
        if(isset($request->limit)) {
            $collection->limit($request->limit);
        }
        $collection = $collection->get();

        if(isset($request->sort)) {
            $collection = $collection->sortBy($request->sort)->values();
        }
        $data = new MessageCollection($collection);
		return $this->successResponse($data);
    }

    public function physician_index(PhysicianIndexRequest $request) {
        $userProfile = auth()->guard('api')->user();
        $collection = Conversation::with('participants', 'participants.profile')->whereRelation('participants', 'user_id', '=', $userProfile->id)->orderBy('created_at', 'desc');
         if(isset($request->limit)) {
            $collection->limit($request->limit);
        }
        $collection = $collection->get();

        if(isset($request->sort)) {
            $collection = $collection->sortBy($request->sort)->values();
        }

        $data = new ConversationCollection($collection);


		return $this->successResponse($data);
    }

    public function listForPatient( $patient_id, $limit = null, $changeReadStatus = true) {
        $userProfile = auth()->guard('api')->user();
        $collection = Message::with(['from'])->where('patient_id', $patient_id)->orderBy('created_at');
        if(isset($limit)) {
            $collection->limit($limit);
        }
        $collection = $collection->get();
        if($changeReadStatus) {
            $collection->each(function ($item) use ($userProfile) {
                if(!$item->is_read){
                    $item->read()->attach($userProfile, ['created_at' => now()]);
                }
                
            });
        }
        $data = new MessageCollection($collection);
		return $this->successResponse($data);
    }

    public function listForPhysician($conversation_id, $limit = null, $changeReadStatus = true) {
        $userProfile = auth()->guard('api')->user();
        $collection = PhysicianMessage::with(['from'])->where('conversation_id', $conversation_id)->orderBy('created_at');

        if(isset($limit)) {
            $collection->limit($limit);
        }
        $collection = $collection->get();
        if($changeReadStatus) {
            $collection->each(function ($item) use ($userProfile) {
                if(!$item->is_read){
                    $item->read()->attach($userProfile, ['created_at' => now()]);
                }
                
            });
        }
        $data = new PhysicianMessageCollection($collection);
		return $this->successResponse($data);
    }

    public function show($id) {
		$message = Message::where('id', $id)->firstOrFail();
		return $this->messageResponse($message);
	}

	public function store(StoreRequest $request) {
        $userProfile = auth()->guard('api')->user();
		$validatedRequest = array_merge(['from_id' => $userProfile->id], $request->validated());
		$message = Message::create($validatedRequest);
        //automatically set read status once created
        $message->read()->attach($userProfile, ['created_at' => now()]);

        if($userProfile->isPhysician) {
            $mailer = new NewPatientMessage($message);
            $patient = PatientProfile::where('id', $validatedRequest['patient_id'])->first();
            if($patient != null) {
                Mail::to($patient->user->email)->send($mailer);
            }
        } else {
            $mailer = new NewPhysicianMessage($message);
            $physicians = PhysicianProfile::get();
            foreach ($physicians as $physician) {
                Mail::to($physician->user->email)->send($mailer);
            }
        }

		return $this->messageResponse($message->load('from'));

	}

    public function physician_store(PhysicianStoreRequest $request) {
        $userProfile = auth()->guard('api')->user();
		$validatedRequest = array_merge(['from_id' => $userProfile->id], $request->validated());
        
        $conversation = null;
        if(!isset($validatedRequest['conversation_id'])) {
            $participantProfile = PhysicianProfile::where('id', $validatedRequest['to_id'])->firstOrFail();
            $participantUser = $participantProfile->user;
            //search for existing conversation id or create a new convo;
            $conversation = Conversation::whereHas('participants', function (Builder $query) use($userProfile, $participantUser) {
                $query
                ->where('conversation_participants.user_id', $userProfile->id)
                ->join(DB::raw('conversation_participants as pc'),
                    'conversation_participants.conversation_id', '=', 'pc.conversation_id')->where('pc.user_id', $participantUser->id);
            })->first();

            
            if($conversation) {
                $validatedRequest['conversation_id'] = $conversation->id;
            } else {
                //create new convo:
                $conversation = Conversation::create([
                    'name' => "New conversation"
                ]);
                $conversation->participants()->attach([$userProfile->id, $participantUser->id ]);
                $validatedRequest['conversation_id'] = $conversation->id;
            }
        } else {
            $conversation = Conversation::where('id', $validatedRequest['conversation_id'])->firstOrFail();
        }

		$message = PhysicianMessage::create($validatedRequest);
        //automatically set read status once created
        $message->read()->attach($userProfile, ['created_at' => now()]);


        foreach($conversation->participants as $participant) {
            if($participant->id == $userProfile->id){
                continue;
            }
            //send email about new message
            $mailer = new NewMessage($message);
            Mail::to($participant->email)->send($mailer);
        }


        $data = new PhysicianMessageResource($message->load('from', 'conversation'));
		return $this->successResponse($data);

	}

    protected function messageResponse(Message $message)
    {
        $data = new MessageResource($message->load('patient'));
		return $this->successResponse($data);
    }

}