<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage as MailContactMessage;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => "required",
            "email" => "required|email",
            "subject" => "required|max:300",
            "message" => "required"
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Couldn't send your message",
                'errors' => $validator->errors()
            ],400);
        }

        try {
            Mail::to(env('ADMIN_EMAIL'))->send(new MailContactMessage($request->input('name'),$request->input('email'),$request->input('subject'),$request->input('message')));
            ContactMessage::create($validator->validate());
            return response()->json([
                'status' => true,
                'message' => "Thanks for your message! We'll contact you soon"
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Couldn't send your message to admin",
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Admin replay message
     */
    public function messageReplay(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => "required",
            "email" => "required|email",
            "subject" => "required|max:300",
            "message" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Couldn't send your message",
                'errors' => $validator->errors()
            ],400);
        }

        try {
            DB::beginTransaction();
            Mail::to($request->input('email'))->send(new MailContactMessage($request->input('name'),$request->input('email'),$request->input('subject'),$request->input('message')));
            $data = $validator->validate();
            if(!empty($request->input('id'))){
                $data['replay_id'] = $request->input('id');
                $data['replay'] = "1";
                ContactMessage::where('id',$request->input('id'))->update([
                    'replay_at' => date('Y-md-d H:i:s')
                ]);
            }
            $data['is_admin'] = "1";
            ContactMessage::create($data);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => "Message successfully sent to ".$request->input('name')
            ],201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "Couldn't send your message to user",
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Seen Message
     */
    public function seenMessage($id){
        try {
            ContactMessage::where('id', $id)->update([
                'seen' => 1,
                'seen_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Message status changed'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Message status couldn't change",
                'data' => ContactMessage::find($id)
            ],400);
        }
    }

    /**
     * Get all user messages
     */
    public function getMessage(){
        return response()->json([
            'status' => true,
            'message' => 'Message successfully retrieved',
            'data' => ContactMessage::where('is_admin','0')->orderBy('id','desc')->get()
        ],200);
    }

    /**
     * Get all admin replay
     */
    public function sentMessage(){
        return response()->json([
            'status' => true,
            'message' => 'Message successfully retrieved',
            'data' => ContactMessage::where('is_admin','1')->orderBy('id','desc')->get()
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            ContactMessage::where('id',$id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Contact message successfully removed'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Contact message couldn\'t remove'
            ],400);
        }
    }
}
