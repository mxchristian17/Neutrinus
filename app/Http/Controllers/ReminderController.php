<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reminder;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function activateNewReminders()
    {
      $reminders = Reminder::where('user_id', auth()->user()->id)->where('reminder_date', '<', Carbon::now())->where('activated', '1')->get();
      $reminders->showAlert = 'false';
      foreach($reminders as $reminder)
      {
        if($reminder->showed == false)
        {
          $reminder->showed = true;
          $reminder->save();
          $reminder->new = true;
          $reminders->showAlert = 'true';
        }else{
          $reminder->new = false;
        }
      }
      return $reminders;
    }

    public function checkNewReminders(request $request)
    {
      $validatedData = $request->validate([
        '_token' => 'required',
        'minId' => 'required|numeric|min:0'
      ]);
      if($request->minId ?? false)
      {
        $minId = $request->minId;
      }else{
        $minId = 0;
      }
      $reminders = Reminder::where('user_id', auth()->user()->id)->where('reminder_date', '<', Carbon::now())->where('activated', '1')->where('id', '>', $minId)->get();
      $reminders->showAlert = 'false';
      foreach($reminders as $reminder)
      {
        if($reminder->showed == false)
        {
          $reminder->showed = true;
          $reminder->save();
          $reminder->new = true;
          $reminders->showAlert = 'true';
        }else{
          $reminder->new = false;
        }
      }
      return $reminders;
    }

    public function newReminder(request $request)
    {
      if(!auth()->user()->permissionUseReminders->state) return 'Acceso no permitido';
      $validatedData = $request->validate([
        '_token' => 'required',
        'datetime' => 'required|date_format:Y-m-d\TH:i',
        'repeat' => 'required|numeric|min:0|max:1',
        'repeat_days_interval' => 'numeric|min:0|nullable',
        'title' => 'required',
        'content' => 'nullable'
      ]);
      $newReminder = new Reminder();
      $newReminder->user_id = auth()->user()->id;
      $newReminder->reminder_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->datetime)->toDateTimeString();
      $newReminder->activated = true;
      $newReminder->showed = false;
      $newReminder->repeat = $request->repeat;
      $newReminder->repeat_days_interval = $request->repeatdaysinterval;
      $newReminder->title = $request->title;
      $newReminder->content = $request->content;
      $newReminder->author_id = auth()->user()->id;
      $newReminder->updater_id = auth()->user()->id;
      if($newReminder->save())
      {
        return ('recordatorio guardado!');
      }else{
        return ('No se pudo guardar el recordatorio. Hubo un error.');
      }
    }

    public function cancelReminder(request $request)
    {
      if(!auth()->user()->permissionUseReminders->state) return 'Acceso no permitido';
      $validatedData = $request->validate([
        '_token' => 'required',
        'id' => 'required|numeric'
      ]);
      $reminder = Reminder::findOrFail($request->id);
      if($reminder->repeat AND $reminder->repeat_days_interval!=null)
      {
        $reminder->reminder_date = Carbon::parse( $reminder->reminder_date)->addDays($reminder->repeat_days_interval);
        $reminder->showed = false;
      }else{
        $reminder->activated = false;
      }
      if($reminder->save())
      {
        if($reminder->activated)
        {
          return 'Se anuló el recordatorio. Se repetirá '.$reminder->reminder_date->diffForHumans();
        }else{
          return 'Se anuló el recordatorio.';
        }
      }else{
        return 'Error al procesar la solicitud';
      }
    }

    public function cancelReminderForEver(request $request)
    {
      if(!auth()->user()->permissionUseReminders->state) return 'Acceso no permitido';
      $validatedData = $request->validate([
        '_token' => 'required',
        'id' => 'required|numeric'
      ]);
      $reminder = Reminder::findOrFail($request->id);
        $reminder->activated = false;
        $reminder->repeat = false;
      if($reminder->save())
      {
        return 'Se anuló el recordatorio.';
      }else{
        return 'Error al procesar la solicitud';
      }
    }

    public function postponeReminder(request $request)
    {
      if(!auth()->user()->permissionUseReminders->state) return 'Acceso no permitido';
      $validatedData = $request->validate([
        '_token' => 'required',
        'id' => 'required|numeric',
        'time' => 'required|numeric|min:1|max:4'
      ]);
      $reminder = Reminder::findOrFail($request->id);
      if($reminder->repeat)
      {
        $newReminder = $reminder->replicate();
        $newReminder->repeat = false;
        switch($request->time)
        {
          case 1: $newReminder->reminder_date = Carbon::now()->addMinutes(10); $newReminder->showed = false; $text = Carbon::now()->addMinutes(10)->diffForHumans(); break; //postpone 10 minutes
          case 2: $newReminder->reminder_date = Carbon::now()->addHours(1); $newReminder->showed = false; $text = Carbon::now()->addHours(1)->diffForHumans(); break; //postpone 1hr
          case 3: $newReminder->reminder_date = Carbon::now()->addDays(1); $newReminder->showed = false; $text = Carbon::now()->addDays(1)->diffForHumans(); break; //postpone 1 day
          case 4: $newReminder->reminder_date = Carbon::now()->addWeeks(1); $newReminder->showed = false; $text = Carbon::now()->addWeeks(1)->diffForHumans(); break; //postpone 1 week
          default: return 'Error al procesar la solicitud';
        }
        if($newReminder->save())
        {
          if($reminder->repeat AND $reminder->repeat_days_interval!=null)
          {
            while(Carbon::parse( $reminder->reminder_date)->addDays($reminder->repeat_days_interval)<Carbon::now()->addDays(1))
            {
              $reminder->reminder_date = Carbon::parse( $reminder->reminder_date)->addDays($reminder->repeat_days_interval);
            }
            $reminder->showed = false;
          }else{
            $reminder->activated = false;
          }
          $reminder->save();
          return 'Recordatorio postpuesto. Se activará nuevamente '.$text;
        }else{
          return 'Error al procesar la solicitud';
        }
      }else{
        switch($request->time)
        {
          case 1: $reminder->reminder_date = Carbon::now()->addMinutes(10); $reminder->showed = false; $text = Carbon::now()->addMinutes(10)->diffForHumans(); break; //postpone 10 minutes
          case 2: $reminder->reminder_date = Carbon::now()->addHours(1); $reminder->showed = false; $text = Carbon::now()->addHours(1)->diffForHumans(); break; //postpone 1hr
          case 3: $reminder->reminder_date = Carbon::now()->addDays(1); $reminder->showed = false; $text = Carbon::now()->addDays(1)->diffForHumans(); break; //postpone 1 day
          case 4: $reminder->reminder_date = Carbon::now()->addWeeks(1); $reminder->showed = false; $text = Carbon::now()->addWeeks(1)->diffForHumans(); break; //postpone 1 week
          default: return 'Error al procesar la solicitud';
        }
        if($reminder->save())
        {
          return 'Recordatorio postpuesto. Se activará nuevamente '.$text;
        }else{
          return 'Error al procesar la solicitud';
        }
      }
    }

}
