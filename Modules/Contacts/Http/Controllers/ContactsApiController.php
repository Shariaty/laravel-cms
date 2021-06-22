<?php

namespace Modules\Contacts\Http\Controllers;

use App\Facade\Facades\OilSettings;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Contacts\Contact;

class ContactsApiController extends Controller
{
    public function getContactInfo()
    {
        $contactInfo = OilSettings::get(['social', 'map' , 'site']);
        return response()->json($contactInfo);
    }

    public function contactMessageSend(Request $request){

        Validator::make($request->all() , [
            'sender'  => 'required|email',
            'subject' => 'max:50',
            'message' => 'required|max:255',
        ])->validate();

        $data = [
            'sender'  => $request->input('sender'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
        ];
        $result =  Contact::create($data);

        if($result){
            return response(['status' => 'success' , 'message' => LBL_CONTACT_MESSAGE_SEND ]);
        }
        return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR] , 404);
    }
}
