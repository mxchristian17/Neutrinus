<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Message;
use App\User;

class MessageController extends Controller
{
  public function showChat(Request $request)
  {
   if(!auth()->user()->permissionUseChat->state){
    return;
   }
   if($request->get('id'))
   {
    $contact_id = $request->get('id');
    if($contact_id!=''){
      $messages = Message::where([['user_sender_id', '=', auth()->user()->id], ['user_receiver_id', '=', $contact_id], ['status', '=', '0'], ['id', '<', $request->startMessage], ['id', '>', $request->lastMessage]])
                          ->orWhere([['user_receiver_id', '=', auth()->user()->id], ['user_sender_id', '=', $contact_id], ['status', '=', '0'], ['id', '<', $request->startMessage], ['id', '>', $request->lastMessage]])
                          ->orderBy('id', 'desc')
                          ->limit(15)
                          ->get();
      $messages = $messages->reverse();
      $output[0] = '<div class="message_container">';
      $output[1] = $request->startMessage;
      $output[2] = $request->lastMessage;
      $contact = User::findOrFail($contact_id);
      foreach($messages as $key => $row)
      {
          $seen = '';
          if(intval($row->user_sender_id) == intval(auth()->user()->id) AND ($row->message != ''))
          {
            $name = auth()->user()->name;
            $color = 'greyRed';
            $position = 'left';

            if(!is_null($row->seen))
            {
              $seendate = Carbon::createFromFormat('Y-m-d H:i:s', $row->seen);
              $seen = '<img class="chatTickIcon" alt="Leido" title="Visto '.$seendate->diffForHumans().'" src="'.asset('images/tickIcon.png').'" />';
            }
          }else{
            $name = $contact->name;
            $color = 'greyBlue';
            $position = 'right';
            if(is_null($row->seen))
            {
              $row->seen = Carbon::now();
              $row->save();
            }
          }
          $regexEmoticons = '/[\x{1F600}-\x{1F64F}, \x{1F900}-\x{1F94F}, \x{1F400}-\x{1F44F}]/u';
          $cleanText = preg_replace($regexEmoticons, '', $row->message);
          if($cleanText == '')
          {
            $row->message = '<span class="h1">'.$row->message.'</span>';
          }
          $output[0] .= '
          <div class="message_container" id="msg'.$row->id.'">
            <div class="avatar rounded-circle m-1 ml-2 float-'.$position.' cursor-pointer" onclick="window.open(\''.asset('user/'.$row->user_sender_id).'\', \'_blank\')"><img src="'.route('avatarImg', $row->user_sender_id.'.jpg').'" alt="Avatar" title="'.$name.'" class="img img-responsive full-width"></div>
            <div class="message_content rounded content_color_'.$color.'">'.$row->message.'<br /><small class="text-secondary">'.$row->created_at->diffForHumans().'</small>'.$seen.'</div>
          </div>
          ';
          if($row->id < $output[1]) $output[1] = $row->id;
          if($row->id > $output[2]) $output[2] = $row->id;
      }
      // UPDATE SEEN LAST 15 MESSAGES
      $messagesb = Message::where([['user_sender_id', '=', auth()->user()->id], ['user_receiver_id', '=', $contact_id], ['status', '=', '0']])
                          ->orWhere([['user_receiver_id', '=', auth()->user()->id], ['user_sender_id', '=', $contact_id], ['status', '=', '0']])
                          ->orderBy('id', 'desc')
                          ->limit(15)
                          ->get();
      foreach($messagesb as $row)
      {
        if(intval($row->user_sender_id) == intval(auth()->user()->id))
        {
          if(!is_null($row->seen))
          {
            $seendate = Carbon::createFromFormat('Y-m-d H:i:s', $row->seen);
            $output[3][$row->id] = '<img class="chatTickIcon" alt="Leido" title="Visto '.$seendate->diffForHumans().'" src="'.asset('images/tickIcon.png').'" />';
          }
        }
      }
      // END UPDATE SEEN LAST 15 MESSAGES
      if(count($messages)==0)
      {
        $output[0] = '';
      }
    }else{
      $output[0] = '';
    }
    return $output;
   }
  }

  public function checkUnreadChat(Request $request)
  {
   if(!auth()->user()->permissionUseChat->state){
    return;
   }
   $ids = Message::where([['user_receiver_id', '=', auth()->user()->id], ['seen', '=', null]])
                        ->get('user_sender_id');
   $result = array();
   $newMessages = array();
   $result[0] = array();
   $result[1] = array();
   $result[2]=0;
   foreach($ids as $id){
     if(!array_key_exists($id->user_sender_id, $result[1]))
     {
       $result[1][$id->user_sender_id]=0;
     }
     $result[1][$id->user_sender_id]++;
     $result[2] = $result[2]+1;
     array_push($result[0], $id->user_sender_id);
   }
   return $result;
 }

 public function sendChat(Request $request)
 {
  if(!auth()->user()->permissionUseChat->state){
   return;
  }
  $validatedData = $request->validate([
    '_token' => 'required',
    'id' => 'required|numeric|min:0',
    'message' => 'required|string',
  ]);
  $messageText = strip_tags(nl2br($request->message), '<br>');
  // start avoid double messages
  $prevMessage = Message::latest()->first();
  $oldMessage = $prevMessage->created_at->diffInSeconds(Carbon::now()->subSeconds(2), true);
  if($prevMessage->user_sender_id == auth()->user()->id AND $prevMessage->user_receiver_id == $request->id AND $prevMessage->message == $messageText AND $oldMessage == 0) return true;
  // end avoid double messages
  $message = new Message;
  $message->user_sender_id = auth()->user()->id;
  $message->user_receiver_id = $request->id;
  $message->message = $messageText;
  $message->status = 0;
  $message->seen = null;
  $message->author_id = auth()->user()->id;
  $message->updater_id = auth()->user()->id;
  $saved = $message->save();

  if($saved)
  {
    return true;
  }
 }

}
