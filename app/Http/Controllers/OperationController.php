<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operation_name;
use App\Element;
use App\Operation;
use DB;
use Validator;

class OperationController extends Controller
{
  public function showOperation_names($showAll = 0) {
    if(auth()->user()->permissionViewOperations->state)
    {
      if($showAll){
        $operation_names = Operation_name::where('state_id', '<', '5')->orderBy('name', 'ASC')->get();
      }else{
        $operation_names = Operation_name::where('state_id', '=', '1')->orderBy('name', 'ASC')->get();
      }
    }else
    {
      return redirect('neutrinus/error/405');
    }

    return view('operation_names')->with('operation_names', $operation_names)->with('showAll', $showAll);
  }

  public function createOperation_name()
  {
    if(!auth()->user()->permissionCreateOperation->state)
    {
      return redirect('neutrinus/error/405');
    }
    $general_states = array(1 => 'Tipo de ruta habilitado');
    if(auth()->user()->permissionViewDisabledOperations->state){ $general_states[2] = 'Tipo de ruta deshabilitado';}
    if(auth()->user()->permissionViewHiddenOperations->state){ $general_states[3] = 'Tipo de ruta oculto';}
    return view('operation_names.create')->with('general_states', $general_states);
  }

  public function storeOperation_name(Request $request)
  {
    if(!auth()->user()->permissionCreateOperation->state)
    {
      return redirect('neutrinus/error/405');
    }
    if(!isset($request->description))
    {
      $request->request->add(['description' => '']);
    }
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'usd_for_hour' => 'numeric|min:0',
      'state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'usd_for_hour.numeric' => 'El costo en dolares por hora debe ser numérico',
      'usd_for_hour.min' => 'El costo en dolares por hora no puede ser negativo'
    ]);
    Operation_name::create($request->except('_token'));

    return redirect('/operation_names');
  }

  public function editOperation_name($id)
  {
    $operation_name = Operation_name::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewOperations->state OR !auth()->user()->permissionCreateOperation->state OR ($operation_name->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation_name->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation_name->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
    {
      return redirect('neutrinus/error/405');
    }
    $general_states = array(1 => 'Tipo de ruta habilitado');
    if(auth()->user()->permissionViewDisabledOperations->state){ $general_states[2] = 'Tipo de ruta deshabilitado';}
    if(auth()->user()->permissionViewHiddenOperations->state){ $general_states[3] = 'Tipo de ruta oculto';}
    if(auth()->user()->permissionViewDeletedOperations->state){ $general_states[4] = 'Tipo de ruta eliminado';}
    return view('operation_names.edit')->with('operation_name', $operation_name)->with('general_states', $general_states);
  }

  public function updateOperation_name($id, Request $request)
  {
    $operation_name = Operation_name::findOrFail($id);
    if(!auth()->user()->permissionViewOperations->state OR !auth()->user()->permissionCreateOperation->state OR ($operation_name->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation_name->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation_name->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
    {
      return redirect('neutrinus/error/405');
    }
    if(!isset($request->description))
    {
      $request->request->add(['description' => '']);
    }
    $request->request->add(['author_id' => $operation_name->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'usd_for_hour' => 'numeric|min:0',
      'state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'usd_for_hour.numeric' => 'El costo en dolares por hora debe ser numérico',
      'usd_for_hour.min' => 'El costo en dolares por hora no puede ser negativo'
    ]);

    $input = $request->except('_token');

    $operation_name->fill($input)->save();

    return redirect('operation_names');
  }

  public function deleteOperation_name(Request $request)
  {
      //Operation_name::find($request->id)->delete();
      if(!auth()->user()->permissionDeleteOperation->state)
      {
        return redirect('neutrinus/error/405');
      }
      Operation_name::where('id', $request->id)->update(['state_id' => 4]);

      return redirect('/operation_names');

  }

  public function deleteForEverOperation_name(Request $request)
  {
      if(!auth()->user()->permissionDeleteOperation->state)
      {
        return redirect('neutrinus/error/405');
      }
      Operation_name::findOrFail($request->id)->delete();

      return redirect('/operation_names');

  }

  public function storeOperation(Request $request)
  {
    if(!auth()->user()->permissionCreateOperation->state OR !isset($request->element_id))
    {
      return redirect('neutrinus/error/405');
    }
    if(!isset($request->observation))
    {
      $request->request->add(['observation' => '']);
    }
    if(!isset($request->cnc_program))
    {
      $request->request->add(['cnc_program' => '']);
    }
    $element = Element::findOrFail($request->element_id);
    $request->request->add(['order_number' => $request->order+1]);
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'operation_name_id' => 'required|numeric',
      'order_number' => 'numeric|min:0',
      'observation' => 'max:255',
      'preparation_time' => 'numeric|min:0',
      'manufacturing_time' => 'numeric|min:0',
      'cnc_program' => 'unique:operations|max:255',
      'operation_state_id' => 'required|numeric|min:1|max:4',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'operation_name_id.required' => 'Es necesario incluir una ruta',
    ]);
    Operation::where([['order_number', '>', $request->order], ['element_id', $request->element_id]])->update(['order_number' => DB::raw('order_number+1')]);
    Operation::create($request->except('_token'));

    return redirect(url()->previous());
  }

  public function editOperation($id)
  {
    $operation = Operation::findOrFail($id);
    if(!auth()->user()->permissionViewOperations->state OR !auth()->user()->permissionCreateOperation->state OR ($operation->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
    {
      return redirect('neutrinus/error/405');
    }
    $element = Element::findOrFail($operation->element_id);
    $element->operation = $element->operation->sortBy('order_number');
    $operation_names = visibleOperation_names();
    if($operation_names)
    {
      $operation_names_data = $operation_names;
      $operation_names = $operation_names->pluck('name', 'id');
      $operations_order = $element->operation;
      foreach($operations_order as $operation_order)
      {
        if(($operation_order->operation_state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation_order->operation_state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation_order->operation_state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
        {
          $operations_order = $operations_order->except($operation_order->id);
        }else{
          $operation_order->name = $operation_order->operation_name->name;
          $operation_order->order = $operation_order->order_number;
        }
      }
      $firstOperation = new Operation;
      $firstOperation->order = 0;
      $firstOperation->name = 'Ubicar como primer ruta';
      $firstOperation->order_number = 0;
      $operations_order->add($firstOperation);
      $operations_order = $operations_order->sortByDesc('order_number');
      $operations_order = $operations_order->pluck('name', 'order');
      $operation_states = array(1 => 'Ruta habilitada');
      unset($operations_order[$operation->order_number]);
      if(auth()->user()->permissionViewDisabledOperations->state){ $operation_states[2] = 'Ruta deshabilitada';}
      if(auth()->user()->permissionViewHiddenOperations->state){ $operation_states[3] = 'Ruta oculta';}
    }else{
      $operations_order = '';
      $operation_states = '';
    }

    $general_states = array(1 => 'Operación habilitada');
    $operation->order = $operation->order_number-1;
    if(auth()->user()->permissionViewDisabledOperations->state){ $general_states[2] = 'Operación deshabilitada';}
    if(auth()->user()->permissionViewHiddenOperations->state){ $general_states[3] = 'Operación oculta';}
    if(auth()->user()->permissionViewDeletedOperations->state){ $general_states[4] = 'Operación eliminada';}
    return view('operations.edit')->withOperation($operation)->with('general_states', $general_states)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->withElement($element);
  }

  public function updateOperation($id, Request $request)
  {
    $operation = Operation::findOrFail($id);
    if(!auth()->user()->permissionViewOperations->state OR !auth()->user()->permissionCreateOperation->state OR ($operation->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
    {
      return redirect('neutrinus/error/405');
    }
    if(!isset($request->observation))
    {
      $request->request->add(['observation' => '']);
    }
    if(!isset($request->cnc_program))
    {
      $request->request->add(['cnc_program' => '']);
    }
    $element = Element::findOrFail($request->element_id);
    //$request->request->add(['order_number' => $request->order+1]);
    $request->request->add(['author_id' => $operation->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'operation_name_id' => 'required|numeric',
      'order' => 'numeric|min:0',
      'observation' => 'max:255',
      'preparation_time' => 'numeric|min:0',
      'manufacturing_time' => 'numeric|min:0',
      'cnc_program' => 'unique:operations,cnc_program,'.$operation->id.'|max:255',
      'operation_state_id' => 'required|numeric|min:1|max:4',
      'url' => 'required|url',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'operation_name_id.required' => 'Es necesario incluir una ruta',
    ]);
    if($request->order < $operation->order_number) $request->order++;
    //echo $request->order.' '.$operation->order_number;die();
    if($request->order == $operation->order_number){
      $request->request->add(['order_number' => $request->order]);
    }elseif($request->order == 1){
      Operation::where([['order_number', '<', $operation->order_number], ['order_number', '>=', $request->order], ['element_id', $request->element_id]])->update(['order_number' => DB::raw('order_number+1')]);
      $request->request->add(['order_number' => 1]);
    }elseif($request->order < $operation->order_number){
      Operation::where([['order_number', '<', $operation->order_number], ['order_number', '>=', $request->order], ['element_id', $request->element_id]])->update(['order_number' => DB::raw('order_number+1')]);
      $request->request->add(['order_number' => $request->order]);
    }else{
      Operation::where([['order_number', '>', $operation->order_number], ['order_number', '<=', $request->order], ['element_id', $request->element_id]])->update(['order_number' => DB::raw('order_number-1')]);
      $request->request->add(['order_number' => $request->order]);
    }

    $input = $request->except('_token');

    $operation->fill($input)->save();

    return redirect($request->url);
  }

  public function deleteOperation(Request $request)
  {
      //Operation_name::find($request->id)->delete();
      if(!auth()->user()->permissionDeleteOperation->state)
      {
        return redirect('neutrinus/error/405');
      }
      $request->request->add(['id' => $request->id]);
      $request->request->add(['_token' => $request->_token]);
      $v = Validator::make($request->all(), [
        '_token' => 'required',
        'id' => 'required|numeric|exists:operations,id',
      ]);
      if ($v->fails() OR ($request->_token != csrf_token())) {
        return redirect('neutrinus/error/405');
      }else{
        Operation::where('id', $request->id)->update(['operation_state_id' => 4]);
      }

      return redirect(url()->previous());

  }

  public function deleteForEverOperation(Request $request)
  {
      if(!auth()->user()->permissionDeleteOperation->state)
      {
        return redirect('neutrinus/error/405');
      }
      $request->request->add(['id' => $request->id]);
      $request->request->add(['_token' => $request->_token]);
      $v = Validator::make($request->all(), [
        '_token' => 'required',
        'id' => 'required|numeric|exists:operations,id',
      ]);
      if ($v->fails() OR ($request->_token != csrf_token())) {
        return redirect('neutrinus/error/405');
      }else{
        Operation::findOrFail($request->id)->delete();
      }

      return redirect(url()->previous());

  }

}
