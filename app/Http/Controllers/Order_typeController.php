<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order_type;
use App\User;

class Order_typeController extends Controller
{
  public function showOrder_types($showAll = 0) {
    if(auth()->user()->permissionViewOrder_types->state)
    {
      if($showAll){
        $order_types = Order_type::orderBy('name', 'ASC')->paginate(50);
      }else{
        $order_types = Order_type::where('state_id', '=', '1')->orderBy('name', 'ASC')->paginate(50);
      }
    }else
    {
      return redirect('projects');
    }

    return view('order_types')->with('order_types', $order_types)->with('showAll', $showAll);
  }

  public function create()
  {
    if(!auth()->user()->permissionCreateOrder_type->state)
    {
      return redirect('projects');
    }
    return view('order_types.create');
  }

  public function store(Request $request)
  {
    if(!auth()->user()->permissionCreateOrder_type->state)
    {
      return redirect('projects');
    }
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $request->request->add(['state_id' => 1]);
    if(!isset($request->description)){
      $request->request->add(['description' => '']);
    }
    //Procesamiento de formula
    $brokenDownFormula = explode('$', $request->formulaSend);
    $validatedElements = array();
    $validatedOriginalElements = array();
    foreach($brokenDownFormula as $formulaElement)
    {
      switch($formulaElement)
      {
        case '^2': array_push($validatedElements, '**2');array_push($validatedOriginalElements, '^2');break;
        case '^3': array_push($validatedElements, '**3');array_push($validatedOriginalElements, '^3');break;
        case 'RAIZ(': array_push($validatedElements, 'sqrt(');array_push($validatedOriginalElements, 'RAIZ(');break;
        case '0': case '1': case '2': case '3': case '4': case '5': case '6':
        case '7': case '8': case '9': case '+': case '-': case '*': case '/':
        case '(': case ')'; case ','; array_push($validatedElements, $formulaElement);array_push($validatedOriginalElements, $formulaElement);break;
        case 'π': array_push($validatedElements, 'pi()');array_push($validatedOriginalElements, 'π');break;
        case 'Øext': array_push($validatedElements, 'd_ext');array_push($validatedOriginalElements, 'Øext');break;
        case 'Øint': array_push($validatedElements, 'd_int');array_push($validatedOriginalElements, 'Øint');break;
        case 'LadoA': array_push($validatedElements, 'side_a');array_push($validatedOriginalElements, 'LadoA');break;
        case 'lado_b': array_push($validatedElements, 'side_b');array_push($validatedOriginalElements, 'LadoB');break;
        case 'Largo': array_push($validatedElements, 'large');array_push($validatedOriginalElements, 'Largo');break;
        case 'Ancho': array_push($validatedElements, 'width');array_push($validatedOriginalElements, 'Ancho');break;
        case 'Espesor': array_push($validatedElements, 'thickness');array_push($validatedOriginalElements, 'Espesor');break;
      }
    }
    $lastItem = end($validatedElements);
    switch($lastItem)
    {
      case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': case '0':
      case ')': case '**2': case '**3': case 'd_ext': case 'd_int': case 'side_a': case 'side_b': case 'large':
       case 'width': case 'thickness': case 'pi()':
        $validatedOriginalElements = implode("$", $validatedOriginalElements);
        $validatedElements = implode($validatedElements);
        $request->request->add(['formula' => $validatedElements]);
        $request->request->add(['original_formula' => $validatedOriginalElements]);
      break;
    }

// ESTA LINEA MUESTRA COMO SE DEBE EVALUAR LA FORMULA CUANDO SEA LEIDA!    (eval("echo ($validatedElements);");die();

    //fin de procesamiento de formula

    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required|unique:order_types',
      'state_id' => 'required|numeric',
      'formula' => 'required',
      'original_formula' => 'required',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'name.unique' => 'Ya existe un tipo de pedido con el nombre que intenta registrar',
      'formula.required' => '',
      'original_formula.required' => 'La formula ingresada no está declarada correctamente.'
    ]);
    Order_type::create($request->except('_token'));

    return redirect('/ordertypes');
  }

  public function edit($id)
  {
    $ordertype = Order_Type::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewOrder_Types->state OR !auth()->user()->permissionCreateOrder_Type->state OR ($ordertype->state_id == 2 AND !auth()->user()->permissionViewDisabledOrder_Types->state) OR ($ordertype->state_id == 3 AND !auth()->user()->permissionViewHiddenOrder_Types->state) OR ($ordertype->state_id == 4 AND !auth()->user()->permissionViewDeletedOrder_Types->state))
    {
      return redirect('ordertypes');
    }
    $ordertype->original_formula = explode('$', $ordertype->original_formula);
    $general_states = array(1 => 'Tipo de pedido habilitado');
    if(auth()->user()->permissionViewDisabledOrder_Types->state){ $general_states[2] = 'Tipo de pedido deshabilitado';}
    if(auth()->user()->permissionViewHiddenOrder_Types->state){ $general_states[3] = 'Tipo de pedido oculto';}
    if(auth()->user()->permissionViewDeletedOrder_Types->state){ $general_states[4] = 'Tipo de pedido eliminado';}
    return view('order_types.edit')->withOrdertype($ordertype)->with('general_states', $general_states);
  }

  public function update($id, Request $request)
  {
    $ordertype = Order_Type::findOrFail($id);
    if(!auth()->user()->permissionViewOrder_Types->state OR !auth()->user()->permissionCreateOrder_Type->state OR ($ordertype->state_id == 2 AND !auth()->user()->permissionViewDisabledOrder_Types->state) OR ($ordertype->state_id == 3 AND !auth()->user()->permissionViewHiddenOrder_Types->state) OR ($ordertype->state_id == 4 AND !auth()->user()->permissionViewDeletedOrder_Types->state))
    {
      return redirect('ordertypes');
    }
    $request->request->add(['author_id' => $ordertype->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);

    if(!isset($request->description)){
      $request->request->add(['description' => '']);
    }
    //Procesamiento de formula
    $brokenDownFormula = explode('$', $request->formulaSend);
    $validatedElements = array();
    $validatedOriginalElements = array();
    foreach($brokenDownFormula as $formulaElement)
    {
      switch($formulaElement)
      {
        case '^2': array_push($validatedElements, '**2');array_push($validatedOriginalElements, '^2');break;
        case '^3': array_push($validatedElements, '**3');array_push($validatedOriginalElements, '^3');break;
        case 'RAIZ(': array_push($validatedElements, 'sqrt(');array_push($validatedOriginalElements, 'RAIZ(');break;
        case '0': case '1': case '2': case '3': case '4': case '5': case '6':
        case '7': case '8': case '9': case '+': case '-': case '*': case '/':
        case '(': case ')'; case ','; array_push($validatedElements, $formulaElement);array_push($validatedOriginalElements, $formulaElement);break;
        case 'π': array_push($validatedElements, 'pi()');array_push($validatedOriginalElements, 'π');break;
        case 'Øext': array_push($validatedElements, 'd_ext');array_push($validatedOriginalElements, 'Øext');break;
        case 'Øint': array_push($validatedElements, 'd_int');array_push($validatedOriginalElements, 'Øint');break;
        case 'LadoA': array_push($validatedElements, 'side_a');array_push($validatedOriginalElements, 'LadoA');break;
        case 'lado_b': array_push($validatedElements, 'side_b');array_push($validatedOriginalElements, 'LadoB');break;
        case 'Largo': array_push($validatedElements, 'large');array_push($validatedOriginalElements, 'Largo');break;
        case 'Ancho': array_push($validatedElements, 'width');array_push($validatedOriginalElements, 'Ancho');break;
        case 'Espesor': array_push($validatedElements, 'thickness');array_push($validatedOriginalElements, 'Espesor');break;
      }
    }
    $lastItem = end($validatedElements);
    switch($lastItem)
    {
      case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': case '0':
      case ')': case '**2': case '**3': case 'd_ext': case 'd_int': case 'side_a': case 'side_b': case 'large':
       case 'width': case 'thickness': case 'pi()':
        $validatedOriginalElements = implode("$", $validatedOriginalElements);
        $validatedElements = implode($validatedElements);
        $request->request->add(['formula' => $validatedElements]);
        $request->request->add(['original_formula' => $validatedOriginalElements]);
      break;
    }
// ESTA LINEA MUESTRA COMO SE DEBE EVALUAR LA FORMULA CUANDO SEA LEIDA!    (eval("echo ($validatedElements);");die();

    //fin de procesamiento de formula

    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'state_id' => 'required|numeric',
      'formula' => 'required',
      'original_formula' => 'required',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'name.unique' => 'Ya existe un tipo de pedido con el nombre que intenta registrar',
      'formula.required' => '',
      'original_formula.required' => 'La formula ingresada no está declarada correctamente.'
    ]);

    $input = $request->except('_token');

    $ordertype->fill($input)->save();
    updateElementsMaterialCost(null, $id, null, null, null, null, null, null, null);
    return redirect('ordertypes');
  }

  public function delete(Request $request)
  {
      if(!auth()->user()->permissionDeleteOrder_type->state)
      {
        return redirect('/ordertypes');
      }
      Order_type::where('id', $request->id)->update(['state_id' => 4]);
      updateElementsMaterialCost(null, null, null, null, null, null, null, null, null);
      return redirect('/ordertypes');

  }

  public function deleteForEver(Request $request)
  {
      if(!auth()->user()->permissionDeleteOrder_type->state)
      {
        return redirect('/ordertypes');
      }
      Order_type::findOrFail($request->id)->delete();

      return redirect('/ordertypes');

  }

}
