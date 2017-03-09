<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Nahid\Talk\Facades\Talk;
use Auth;
use View;

class MessageController extends Controller
{
    protected $authUser;
    public function __construct()
    {
        $this->middleware('auth');
        Talk::setAuthUserId(Auth::user()->id);

        View::composer('partials.peoplelist', function($view) {
            $threads = Talk::threads();
            $view->with(compact('threads'));
        });
    }

    public function chatHistory($id)
    {
        $conversations = Talk::getMessagesByUserId($id);
        $user = '';
        $messages = [];
        $conversation = [];
        $cvId = 0;
        if(!$conversations) {
            $user = User::find($id);
        } else {
            $user = $conversations->withUser;
            $messages = $conversations->messages;

            $cvId = $conversations->id;
            $conversation = Talk::getConversationById($cvId);
        }

        return view('messages.conversations', compact('id','messages', 'user', 'conversations', 'conversation'));
    }

    public function updateConversation(Request $request)
    {
        if ($request->ajax()) {
        	$bg = $request->bg;
        	$cvId = $request->data;
        	$val = $bg . ' ' . $cvId;
        	if ($conversation = Talk::updateConversation($cvId, $bg)) {
        		$response = array(
        				'status'=>$conversation->background,
        				'msg'=>'successfully',
        		);
        		return response()->json(['status'=>'successfully '.$conversation->background]);
        	}
        }
        return response()->json(['error' => 'error']);
    }

    public function ajaxSendMessage(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'message-data'=>'required',
                '_id'=>'required'
            ];

            $this->validate($request, $rules);

            $body = $request->input('message-data');
            $userId = $request->input('_id');

            if ($message = Talk::sendMessageByUserId($userId, $body)) {
                $html = view('ajax.newMessageHtml', compact('message'))->render();
                return response()->json(['status'=>'success', 'html'=>$html], 200);
            }
        }
    }

    public function ajaxDeleteMessage(Request $request, $id)
    {
        if ($request->ajax()) {
            if(Talk::deleteMessage($id)) {
                return response()->json(['status'=>'success'], 200);
            }

            return response()->json(['status'=>'errors', 'msg'=>'something went wrong'], 401);
        }
    }

    public function tests()
    {
        dd(Talk::channel());
    }
}
