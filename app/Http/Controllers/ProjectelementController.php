<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Projectelement;
use App\Element;
use App\Subset;
use App\Project;
use App\Material;
use App\Order_type;
use App\Operation;
use App\Todo;
use Session;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\ElementController;
/*use File;
use Response;*/
use setasign\Fpdi\Fpdi;

class ProjectelementController extends Controller
{
  private function operationCost($projectelement, $operation_id)
  {
    $operation = Operation::findOrFail($operation_id);
    $cost = round((($operation->preparationUsdCost/$projectelement->element->quantity_per_manufacturing_series)+($operation->manufacturingUsdCost*$projectelement->quantity)), 2);
    return $cost;
  }
  public function projectelementDirectCost($projectelement)
  {
    if(auth()->user()->permissionViewOperationPrice->state)
    {
      $directCost = 0;
      foreach ($projectelement->element->operation as $operation)
      {
        $operation->cost = $this->operationCost($projectelement, $operation->id);
        if($operation->operation_state_id == 1 OR $operation->operation_state_id == 3)
        {
          $directCost = $directCost + $operation->cost;
        }
      }
      $projectelement->directCost = $directCost;
    }
    return $projectelement;
  }
  public function showProjectelement($id, $showAllOperations = 0)
  {
      $projectelement = Projectelement::findOrFail($id);
      if(!auth()->user()->permissionViewElements->state OR ($projectelement->specific_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->specific_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->specific_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
      {
        return redirect('projects');
      }
      $projectelement = $this->projectelementDirectCost($projectelement);
      if(!$projectelement) return redirect('neutrinus/error/405');
      $projectelement->element->operation = $projectelement->element->operation->sortBy('order_number');
      $operation_names = visibleOperation_names();
      if($operation_names)
      {
        $operation_names_data = $operation_names;
        $operation_names = $operation_names->pluck('name', 'id');

        $operations_order = $projectelement->element->operation;
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
        $firstOperation->name = 'Añadir como primer ruta';
        $firstOperation->order_number = 0;
        $operations_order->add($firstOperation);
        $operations_order = $operations_order->sortByDesc('order_number');
        $operations_order = $operations_order->pluck('name', 'order');
        $operation_states = array(1 => 'Ruta habilitada');
        if(auth()->user()->permissionViewDisabledOperations->state){ $operation_states[2] = 'Ruta deshabilitada';}
        if(auth()->user()->permissionViewHiddenOperations->state){ $operation_states[3] = 'Ruta oculta';}
      }else{
        $operations_order = '';
        $operation_states = '';
      }

      if(auth()->user()->permissionViewOwedItems)
      {
        $todo = Todo::where('element_id', $projectelement->element->id)->where('state', 1)->get();
        $todo = $todo->sortBy(function($todo){
            return $todo->sale->requested_delivery_date;
        });
        $quantity = 0;
        $todoCrono = array();
        $prevItemTodo = null;
        foreach($todo as $itemTodo)
        {
          if($itemTodo->sale->TodoActive)
          {
            $quantity += $itemTodo->sale_item->quantity;
            if($prevItemTodo == null)
            {
              array_push($todoCrono, array($itemTodo->sale->requested_delivery_date, $itemTodo->sale_item->quantity));
            }else{
              if($prevItemTodo->sale->requested_delivery_date == $itemTodo->sale->requested_delivery_date)
              {
                $prevCrono = array_pop($todoCrono);
                array_push($todoCrono, array($prevItemTodo->sale->requested_delivery_date, $prevCrono[1]+$itemTodo->sale_item->quantity));
              }else{
                array_push($todoCrono, array($itemTodo->sale->requested_delivery_date, $itemTodo->sale_item->quantity));
              }
            }
            $prevItemTodo = $itemTodo;
          }
        }
        $todoQty = $quantity;
      }else{
        $todo = null;
        $todoQty = null;
      }

      return view('projectelement')->with('projectelement', $projectelement)->with('showAllOperations', $showAllOperations)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->with('todoQty', $todoQty)->withTodo($todoCrono);
  }

  public function showExt_f_1($id) //Reservado solo para archivos pdf
  {
    $projectelement = Projectelement::findOrFail($id);
    if(!auth()->user()->permissionViewElementsExt_f_1->state OR !auth()->user()->permissionViewElements->state OR ($projectelement->specific_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->specific_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->specific_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('neutrinus/error/405');
    }
    $pdf = new Fpdi();
    $pdf->setSourceFile(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1'));
    $tplIdx = $pdf -> importPage(1);
    $size = $pdf->getTemplateSize($tplIdx);
    $pdf -> AddPage();
    $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'],FALSE);
    /*$pdf -> SetFont('Arial');
    $pdf -> SetTextColor(0, 0, 0);
    $pdf -> SetXY(18, 174);
    $pdf -> Write(0, 'hola');*/
    $pdf -> Output('I', $projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1'));
  }

  public function showExt_f_2($id) //Reservado solo para archivos pdf
  {
    $projectelement = Projectelement::findOrFail($id);
    if(!auth()->user()->permissionViewElements->state OR ($projectelement->specific_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->specific_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->specific_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('neutrinus/error/405');
    }
    /*$file = File::get(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.ext_f_1);
    $response = Response::make($file, 200);
    $response->header('Content-Type', 'application/pdf');
    $response->header('Content-Disposition', "inline; filename=".$projectelement->element->nro.'-'.$projectelement->element->add.' - '.$projectelement->element->name.'.'.ext_f_1);
    return $response;*/
    $pdf = new Fpdi();
    $pdf->setSourceFile(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1'));
    $tplIdx = $pdf -> importPage(1);
    $size = $pdf->getTemplateSize($tplIdx);
    $pdf -> AddPage();
    $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'],FALSE);
    /*$pdf -> SetFont('Arial');
    $pdf -> SetTextColor(0, 0, 0);
    $pdf -> SetXY(18, 174);
    $pdf -> Write(0, 'hola');*/
    $pdf -> Output('I', $projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1'));
  }

  public function create($project_id, $subset_id)
  {
    if(!auth()->user()->permissionCreateProjectelement->state)
    {
      return redirect("project/$project_id");
    }
    $subset = Subset::findOrFail($subset_id);
    if(($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state))
    {
      return redirect("project/$project_id");
    }

    $materials = Material::all();
    foreach ($materials as $material)
    {
      if(($material->state_id == 2 AND !auth()->user()->permissionViewDisabledMaterials->state) OR ($material->state_id == 3 AND !auth()->user()->permissionViewHiddenMaterials->state) OR ($material->state_id == 4 AND !auth()->user()->permissionViewDeletedMaterials->state))
      {
        $materials = $materials->except($material->id);
      }
    }
    $materials = $materials->pluck('name', 'id');

    $order_types = Order_type::all();
    foreach ($order_types as $order_type)
    {
      if(($order_type->state_id == 2 AND !auth()->user()->permissionViewDisabledOrder_types->state) OR ($order_type->state_id == 3 AND !auth()->user()->permissionViewHiddenOrder_types->state) OR ($order_type->state_id == 4 AND !auth()->user()->permissionViewDeletedOrder_types->state))
      {
        $order_types = $order_types->except($order_type->id);
      }
    }
    $order_types_data = $order_types;
    $order_types = $order_types->pluck('name', 'id');

    $project = Project::findOrFail($project_id);
    return view('projectelements.create')->with('project', $project)->with('subset', $subset_id)->withMaterials($materials)->with('order_types', $order_types)->with('order_types_data', $order_types_data)/*->with('subsets', $subsets)*/;
  }

  public function store(Request $request)
  {
    if(!auth()->user()->permissionCreateProjectelement->state)
    {
      return redirect('neutrinus/error/405');
    }

    if($request->newType)
    {
      $request->request->add(['element_id' => '0']);
      if(!auth()->user()->permissionCreateElement->state)
      {
        return redirect("projects");
      }
    }

    if($request->element!=''){
      $element = explode ("(", $request->element);
      $element = last($element);
      $element = str_replace(")", "", $element);
      $element = explode("-", $element);
      if(is_numeric($element[0]) AND is_numeric($element[1]))
      {
        $element = Element::where([['nro', '=', intval($element[0])], ['add', '=', intval($element[1])]])->first();
        $request->request->add(['element_id' => $element->id]);
      }else{
        $request->request->add(['element_id' => 'Incorrecto']);
      }
    }

    $part = Projectelement::where([['project_id', '=', intval($request->project_id)], ['subset_id', '=', intval($request->subset_id)]])->max('part');
    //echo $part; die();
    if($part == null){
      $part = 1;
    } else {
      $part = $part + 1;
    }
    $request->request->add(['part' => $part]);
    $request->request->add(['subpart' => '0']);
    $request->request->add(['version' => '0']);
    $request->request->add(['welded_set' => '0']);
    $request->request->add(['specific_state_id' => '1']);

    $validatedData = $request->validate([
      '_token' => 'required',
      'element_id' => 'required|numeric',
      'subset_id' => 'required|numeric|max:1000',
      'quantity' => 'required|numeric|max:99999',
      'purchase_order' => 'required|numeric|max:99999',
      'manufacturing_order' => 'required|numeric|max:99999',
      'project_id' => 'required|numeric|max:99999',
      'specific_state_id' => 'required|numeric|min:1|max:7',
      'author_id' => 'required|numeric|max:99999',
      'updater_id' => 'required|numeric|max:99999'
    ],
    [
      'element_id.required' => 'Es necesario incluir un elemento',
      'element_id.numeric' => 'El elemento no se ha ingresado de forma correcta',
      'subset.required' => 'Es necesario seleccionar un subconjunto. Si no hay ninguno, debes crear uno primero',
    ]);

    if($request->newType)
    {
      if(auth()->user()->permissionCreateElement->state)
      {
        $request->request->add(['general_state_id' => '1']);
        $request['element_id'] = (new ElementController)->store($request, 0);
      }else{
        return redirect('neutrinus/error/405');
      }
    }

    $projectelement = Projectelement::create($request->except('_token'));
    $id = $projectelement->id;
    $project_id = $projectelement->project_id;
    return redirect("/project/$project_id");
  }

  public function edit($id)
  {
    $projectelement = Projectelement::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewElements->state OR !auth()->user()->permissionCreateProjectelement->state OR ($projectelement->state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect("/project/$projectelement->project_id");
    }
    $subsets = Subset::where('project_id', '=', $projectelement->project_id)->get();
    foreach ($subsets as $subset)
    {
      if(($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state))
      {
        $subsets = $subsets->except($subset->id);
      }
    }
    $subsets = $subsets->pluck('name', 'id');
    $general_states = array(1 => 'Elemento de proyecto habilitado');
    if(auth()->user()->permissionViewDisabledElements->state){ $general_states[2] = 'Elemento de proyecto deshabilitado';}
    if(auth()->user()->permissionViewHiddenElements->state){ $general_states[3] = 'Elemento de proyecto oculto';}
    if(auth()->user()->permissionViewDeletedElements->state){ $general_states[4] = 'Elemento de proyecto eliminado';}
    return view('projectelements.edit')->withProjectelement($projectelement)->with('general_states', $general_states)->withSubsets($subsets);
  }

  public function update($id, Request $request)
  {
    if(!auth()->user()->permissionCreateProjectelement->state)
    {
      return redirect('neutrinus/error/405');
    }
    $projectelement = Projectelement::findOrFail($id);
    if(!auth()->user()->permissionViewElements->state OR !auth()->user()->permissionCreateProjectelement->state OR ($projectelement->state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect("/project/$projectelement->project_id");
    }
    if($request->element!=''){
      $element = explode ("(", $request->element);
      $element = last($element);
      $element = str_replace(")", "", $element);
      $element = explode("-", $element);
      $element = Element::where([['nro', '=', intval($element[0])], ['add', '=', intval($element[1])]])->first();
      $request->request->add(['element_id' => $element->id]);
    }
    if($projectelement->subset_id != $request->subset)
    {
      $part = Projectelement::where([['project_id', '=', intval($projectelement->project_id)], ['subset_id', '=', intval($request->subset)]])->max('part');
      if($part == null){
        $part = 1;
      } else {
        $part = $part + 1;
      }
      $request->request->add(['part' => $part]);
      $request->request->add(['subpart' => '0']);
      $request->request->add(['version' => '0']);
      $request->request->add(['welded_set' => '0']);
    }
    $request->request->add(['subset_id' => $request->subset]);
    $request->request->add(['author_id' => $projectelement->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'element_id' => 'required|numeric',
      'subset_id' => 'required|numeric',
      'quantity' => 'required|numeric|min:0',
      'purchase_order' => 'required|numeric',
      'manufacturing_order' => 'required|numeric',
      'specific_state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'element_id.required' => 'Es necesario incluir un elemento general de partida',
      'subset_id.required' => 'Es necesario incluir un subconjunto al cual pertenece el elemento en el proyecto',
      'subset_id.numeric' => 'El formato de subconjunto no es válido',
      'quantity.required' => 'Es necesario incluir una cantidad',
      'quantity.numeric' => 'La cantidad debe ser un valor numérico',
      'quantity.min' => 'La cantidad debe ser mayor a 0',
      'purchase_order.required' => 'Debe incluirse un órden para la compra',
      'purchase_order.numeric' => 'El orden para la compra debe ser numérico',
      'manufacturing_order.required' => 'Debe incluirse un órden para la fabricación',
      'manufacturing_order.numeric' => 'El órden para la fabricación debe ser numérico',
      'specific_state_id.required' => 'Es necesario incluir un estado de elemento de proyecto',
      'specific_state_id.numeric' => 'El estado de elemento de proyecto no es válido'
    ]);

    $input = $request->except('_token');

    $projectelement->fill($input)->save();

    return redirect("/projectelement/$projectelement->id");
  }

  public function delete(Request $request)
  {
      //Material::find($request->id)->delete();
      if(!auth()->user()->permissionDeleteProjectelement->state)
      {
        return redirect("/project/$projectelement->project_id");
      }
      $projectelement = Projectelement::where('id', $request->id)->update(['specific_state_id' => 4]);
      $projectelement = Projectelement::findOrFail($request->id);
      return redirect("/project/$projectelement->project_id");

  }

  public function deleteForEver(Request $request)
  {
      if(!auth()->user()->permissionDeleteProjectelement->state)
      {
        return redirect("/project/$projectelement->project_id");
      }
      $projectelement = Projectelement::findOrFail($request->id);
      Projectelement::findOrFail($request->id)->delete();

      return redirect("/project/$projectelement->project_id");

  }

  public function copyTo(Request $request)
  {
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'project_id' => 'required|numeric|exists:projects,id',
      'subset_id' => 'required|numeric|exists:subsets,id',
      'id' => 'required|numeric|exists:projectelements',
      'author_id' => 'required',
      'updater_id' => 'required'
    ]);

    if(!auth()->user()->permissionCreateProjectelement->state)
    {
      return redirect('neutrinus/error/405');
    }
    $projectelement = Projectelement::findOrFail($request->id);
    $newProject = projectIsVisible($request->project_id);
    $newSubset = subsetIsVisible($request->subset_id);
    if(!auth()->user()->permissionViewElements->state OR !auth()->user()->permissionCreateProjectelement->state OR ($projectelement->state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($projectelement->state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($projectelement->state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect("/project/$projectelement->project_id");
    }
    if($request->project_id=='' OR $request->subset_id=='' OR !$newSubset OR !$newProject){
      return redirect('neutrinus/error/405');
    }


    $newProjectelement = $projectelement->replicate();
    $part = Projectelement::where([['project_id', '=', intval($request->project_id)], ['subset_id', '=', intval($request->subset_id)]])->max('part');
    if($part == null){
      $part = 1;
    } else {
      $part = $part + 1;
    }
    $newProjectelement->project_id = $request->project_id;
    $newProjectelement->subset_id = $request->subset_id;
    $newProjectelement->part = $part;
    $newProjectelement->subpart = 0;
    $newProjectelement->version = 0;
    $newProjectelement->welded_set = 0;
    $newProjectelement->push();

    Session::flash('message.level', 'success');
    Session::flash('status', 'La copia de '.$newProjectelement->element->name.' ha sido generada con exito al subconjunto '.$newSubset->name.' del proyecto '.$newProject->name.'.');
    return redirect(url()->previous());
  }

}
