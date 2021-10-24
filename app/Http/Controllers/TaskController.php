<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;
use Carbon\Carbon;

class TaskController extends Controller
{
  public function activateNewTasks()
  {
    $tasks = Task::where('user_id', auth()->user()->id)->where('task_start', '<', Carbon::now())->where('activated', '1')->get();
    $tasks->showAlert = 'false';
    foreach($tasks as $task)
    {
      if(($task->showed == false) AND ($task->user_id == auth()->user()->id))
      {
        $task->showed = true;
        $task->save();
        $task->new = true;
        $tasks->showAlert = 'true';
      }else{
        $task->new = false;
      }
    }
    return $tasks;
  }

  public function checkNewTasks(request $request)
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
    $tasks = Task::where('user_id', auth()->user()->id)->where('task_start', '<', Carbon::now())->where('activated', '1')->where('id', '>', $minId)->get();
    $tasks->showAlert = 'false';
    foreach($tasks as $task)
    {
      if($task->showed == false AND ($task->user_id == auth()->user()->id))
      {
        $task->showed = true;
        $task->save();
        $task->new = true;
        $tasks->showAlert = 'true';
      }else{
        $task->new = false;
      }
      $task->task_start = Carbon::parse($task->start)->diffForHumans();
      $task->task_estimated_end = Carbon::parse($task->task_estimated_end)->diffForHumans();
      if(is_null($task->content)) $task->content = '';
      $task->authorName = $task->author->name;
    }
    return $tasks;
  }

  public function newTask(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'datetime' => 'required|date_format:Y-m-d\TH:i',
      'user_id' => 'required|numeric',
      'estimatedEnd' => 'required|date_format:Y-m-d\TH:i',
      'repeat' => 'required|numeric|min:0|max:1',
      'repeat_days_interval' => 'numeric|min:0|nullable',
      'title' => 'required',
      'content' => 'nullable'
    ]);
    $targetUser = User::findOrFail(intval($request->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $request->user_id))
    {
      return 'El usuario al que intenta asignar una tarea no se encuentra bajo su mando';
    }
    $newTask = new Task();
    $newTask->user_id = $request->user_id;
    $newTask->task_start = Carbon::createFromFormat('Y-m-d\TH:i', $request->datetime)->toDateTimeString();
    $newTask->task_estimated_end = Carbon::createFromFormat('Y-m-d\TH:i', $request->estimatedEnd)->toDateTimeString();
    $newTask->activated = true;
    $newTask->percentage = 0;
    $newTask->showed = false;
    $newTask->repeat = $request->repeat;
    $newTask->repeat_days_interval = $request->repeatdaysinterval;
    $newTask->title = $request->title;
    $newTask->content = $request->content;
    $newTask->author_id = auth()->user()->id;
    $newTask->updater_id = auth()->user()->id;
    if($newTask->save())
    {
      return ('tarea guardada!');
    }else{
      return ('No se pudo guardar la tarea. Hubo un error.');
    }
  }

  public function endTask(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'id' => 'required|numeric'
    ]);
    $task = Task::findOrFail($request->id);
    $targetUser = User::findOrFail(intval($task->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $task->user_id)) return 'Acceso no permitido';
    if($task->repeat AND $task->repeat_days_interval!=null)
    {
      $task->task_start = Carbon::parse( $task->task_start)->addDays($task->repeat_days_interval);
      $task->showed = false;
    }else{
      $task->activated = false;
      $task->percentage = 100;
    }
    if($task->save())
    {
      if($task->activated)
      {
        return 'Se finalizó la tarea. Se repetirá '.$task->task_start->diffForHumans();
      }else{
        return '✓ Se finalizó la tarea';
      }
    }else{
      return 'Error al procesar la solicitud';
    }
  }

  public function changeTaskPercentage(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'percentage' => 'required|numeric|min:0|max:99',
      'id' => 'required|numeric'
    ]);
    $task = Task::findOrFail($request->id);
    $targetUser = User::findOrFail(intval($task->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $task->user_id)) return 'Acceso no permitido';
    $task->percentage = $request->percentage;
    if($task->save())
    {
      return 'Porcentaje de avance modificado';
    }else{
      return 'Error al procesar la solicitud';
    }
  }

  public function cancelTask(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'id' => 'required|numeric'
    ]);
    $task = Task::findOrFail($request->id);
    $targetUser = User::findOrFail(intval($task->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $task->user_id)) return 'Acceso no permitido';
    if($task->repeat AND $task->repeat_days_interval!=null)
    {
      $task->task_start = Carbon::parse( $task->task_start)->addDays($task->repeat_days_interval);
      $task->showed = false;
    }else{
      $task->activated = false;
    }
    if($task->save())
    {
      if($task->activated)
      {
        return 'Se anuló la tarea. Se repetirá '.$task->task_start->diffForHumans();
      }else{
        return 'Se anuló la tarea.';
      }
    }else{
      return 'Error al procesar la solicitud';
    }
  }

  public function cancelTaskForEver(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'id' => 'required|numeric'
    ]);
    $task = Task::findOrFail($request->id);
    $targetUser = User::findOrFail(intval($task->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $task->user_id)) return 'Acceso no permitido';
    $task->activated = false;
    $task->repeat = false;
    if($task->save())
    {
      return 'Se anuló la tarea.';
    }else{
      return 'Error al procesar la solicitud';
    }
  }

  public function postponeTask(request $request)
  {
    if(!auth()->user()->permissionUseTasks->state) return 'Acceso no permitido';
    $validatedData = $request->validate([
      '_token' => 'required',
      'id' => 'required|numeric',
      'time' => 'required|numeric|min:1|max:4'
    ]);
    $task = Task::findOrFail($request->id);
    $targetUser = User::findOrFail(intval($task->user_id));
    if((!isUnderCharge(auth()->user(), $targetUser)) AND (auth()->user()->id != $task->user_id)) return 'Acceso no permitido';
    if($task->repeat)
    {
      $newTask = $task->replicate();
      $newTask->repeat = false;
      switch($request->time)
      {
        case 1: $newTask->task_start = Carbon::now()->addMinutes(10); $newTask->showed = false; $text = Carbon::now()->addMinutes(10)->diffForHumans(); break; //postpone 10 minutes
        case 2: $newTask->task_start = Carbon::now()->addHours(1); $newTask->showed = false; $text = Carbon::now()->addHours(1)->diffForHumans(); break; //postpone 1hr
        case 3: $newTask->task_start = Carbon::now()->addDays(1); $newTask->showed = false; $text = Carbon::now()->addDays(1)->diffForHumans(); break; //postpone 1 day
        case 4: $newTask->task_start = Carbon::now()->addWeeks(1); $newTask->showed = false; $text = Carbon::now()->addWeeks(1)->diffForHumans(); break; //postpone 1 week
        default: return 'Error al procesar la solicitud';
      }
      if($newTask->save())
      {
        if($task->repeat AND $task->repeat_days_interval!=null)
        {
          while(Carbon::parse( $task->task_start)->addDays($task->repeat_days_interval)<Carbon::now()->addDays(1))
          {
            $task->task_start = Carbon::parse( $task->task_start)->addDays($task->repeat_days_interval);
          }
          $task->showed = false;
        }else{
          $task->activated = false;
        }
        $task->save();
        return 'Recordatorio postpuesto. Se activará nuevamente '.$text;
      }else{
        return 'Error al procesar la solicitud';
      }
    }else{
      switch($request->time)
      {
        case 1: $task->task_start = Carbon::now()->addMinutes(10); $task->showed = false; $text = Carbon::now()->addMinutes(10)->diffForHumans(); break; //postpone 10 minutes
        case 2: $task->task_start = Carbon::now()->addHours(1); $task->showed = false; $text = Carbon::now()->addHours(1)->diffForHumans(); break; //postpone 1hr
        case 3: $task->task_start = Carbon::now()->addDays(1); $task->showed = false; $text = Carbon::now()->addDays(1)->diffForHumans(); break; //postpone 1 day
        case 4: $task->task_start = Carbon::now()->addWeeks(1); $task->showed = false; $text = Carbon::now()->addWeeks(1)->diffForHumans(); break; //postpone 1 week
        default: return 'Error al procesar la solicitud';
      }
      if($task->save())
      {
        return 'Tarea postpuesta. Se activará nuevamente '.$text;
      }else{
        return 'Error al procesar la solicitud';
      }
    }
  }
}
