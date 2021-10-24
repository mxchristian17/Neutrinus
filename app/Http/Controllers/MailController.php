<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class MailController extends Controller
{
    public function sendMail($to, $subject, $message, $attachment)
    {
      $destination = storage_path('app').'/files/Temp/'.$attachment[1];
      \File::copy($attachment[0],$destination);
      $details = [
          'attachment' => $destination,
          'subject' => $subject,
          'body' => $message,
          'user' => auth()->user()->name,
          'from' => auth()->user()->email,
          'company_name' => config('constants.company_name'),
          'company_address' => config('constants.company_address'),
          'company_phone_number' => config('constants.company_phone_number')
      ];
      foreach ($to as $recipient) {
          Mail::to($recipient)->send(new \App\Mail\sendMail($details));
      }
      \File::delete($destination);
      return;
    }
}
