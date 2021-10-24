<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Element;
use Illuminate\Support\Facades\Gate;
use App\User;
use App\Material;
use App\Order_type;
use setasign\Fpdi\Fpdi;
use App\Operation;
use App\Todo;
use Carbon\Carbon;
use App\Supplier;
use App\Supplier_code;
use Validator;

class ElementController extends Controller
{

  private function operationCost($element, $operation_id)
  {
    $operation = Operation::findOrFail($operation_id);
    $cost = round((($operation->preparationUsdCost/$element->quantity_per_manufacturing_series)+($operation->manufacturingUsdCost)), 2);
    return $cost;
  }

  private function lookForElements($showAll, $query)
  {
    if(!(auth()->user()->permissionViewElements->state))
    {$query = '$/&""%¿¿¿284325jlfdgngnggnkndlfvlddofgieorgj';}
    if(!(auth()->user()->permissionViewDisabledElements->state AND $showAll))
    {$viewDisabled = 2;}else{$viewDisabled = 0;}
    if(!(auth()->user()->permissionViewHiddenElements->state AND $showAll))
    {$viewHidden = 3;}else{$viewHidden = 0;}
    if(!(auth()->user()->permissionViewDeletedElements->state AND $showAll))
    {$viewDeleted = 4;}else{$viewDeleted = 0;}

    $position = (strlen(strrchr($query, ' (')));
    if(strpos($query, ' (')) $query = substr($query, 0, -($position));

    $elements = Element::where(function ($q) use($viewDisabled, $viewHidden, $viewDeleted, $query) {
      $q->where('general_state_id', '!=', $viewDisabled)
            ->where('general_state_id', '!=', $viewHidden)
            ->where('general_state_id', '!=', $viewDeleted)
            ->where('name', 'LIKE', "%{$query}%");
    })->orWhere(function($q) use($viewDisabled, $viewHidden, $viewDeleted, $query) {
      $q->where('general_state_id', '!=', $viewDisabled)
            ->where('general_state_id', '!=', $viewHidden)
            ->where('general_state_id', '!=', $viewDeleted)
            ->where('nro', 'LIKE', "%{$query}%");
    })->orderBy('name', 'ASC')->paginate(20);

    return $elements;
  }

  public function showElements($showAll = 0) {
    if(!auth()->user()->permissionViewElements->state)
    {
      return redirect('projects');
    }

    $elements = $this->lookForElements($showAll, '');
    return view('elements')->with('elements', $elements)->withShowAll($showAll)->withoptionShowAll(1);

  }

  public function showElement($id, $showAllOperations = 0) {

    $element = Element::findOrFail($id);
    if(!auth()->user()->permissionViewElements->state OR ($element->general_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($element->general_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($element->general_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('projects');
    }
    if(auth()->user()->permissionViewOperationPrice->state)
    {
      $directCost = 0;
      foreach ($element->operation as $operation)
      {
        $operation->cost = $this->operationCost($element, $operation->id);
        if($operation->operation_state_id == 1 OR $operation->operation_state_id == 3)
        {
          $directCost = $directCost + $operation->cost;
        }
      }
      $element->directCost = $directCost;
    }

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
      $todo = Todo::where('element_id', $id)->where('state', 1)->get();
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

    if(auth()->user()->permissionEditSupplier_code->state)
    {
      $supplier_id = visibleSuppliers(false, 1, true, false, true);
    }

    switch($element->general_state_id){
      case 1:
        return view('element')->with('element', $element)->with('showAllOperations', $showAllOperations)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->with('todoQty', $todoQty)->withTodo($todoCrono)->with('supplier_id', $supplier_id);
      break;
      case 2:
        if(auth()->user()->permissionViewDisabledElements->state){
          return view('element')->with('element', $element)->with('showAllOperations', $showAllOperations)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->with('todoQty', $todoQty)->withTodo($todoCrono)->with('supplier_id', $supplier_id);
        }
      break;
      case 3:
        if(auth()->user()->permissionViewHiddenElements->state){
          return view('element')->with('element', $element)->with('showAllOperations', $showAllOperations)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->with('todoQty', $todoQty)->withTodo($todoCrono)->with('supplier_id', $supplier_id);
        }
      break;
      case 4:
        if(auth()->user()->permissionViewDeletedElements->state){
          return view('element')->with('element', $element)->with('showAllOperations', $showAllOperations)->with('operation_names', $operation_names)->with('operations_order', $operations_order)->with('operation_states', $operation_states)->withTodoQty($todoQty)->withTodo($todoCrono)->with('supplier_id', $supplier_id);
        }
      break;
    }

    return redirect('projects');

  }

  public function showExt_f_1($id) //Reservado solo para archivos pdf
  {
    $element = Element::findOrFail($id);
    if(!auth()->user()->permissionViewElementsExt_f_1->state OR !auth()->user()->permissionViewElements->state OR ($element->specific_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($element->specific_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($element->specific_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('projects');
    }
    $pdf = new Fpdi();
    $pdf->setSourceFile(storage_path('app').'/files/ext_f_1/elements/'.$element->nro.'-'.$element->add.'.'.config('constants.ext_f_1'));
    $tplIdx = $pdf -> importPage(1);
    $size = $pdf->getTemplateSize($tplIdx);
    $pdf -> AddPage();
    $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'],FALSE);
    /*$pdf -> SetFont('Arial');
    $pdf -> SetTextColor(0, 0, 0);
    $pdf -> SetXY(18, 174);
    $pdf -> Write(0, 'hola');*/
    $pdf -> Output('I', $element->nro.'-'.$element->add.'.'.config('constants.ext_f_1'));
  }

  public function create()
  {
    if(!auth()->user()->permissionCreateElement->state)
    {
      return redirect('projects');
    }

    $materials = visibleMaterials();if(!$materials) return redirect('neutrinus/error/405');
    $materials = $materials->pluck('name', 'id');

    $order_types = visibleOrder_types();if(!$order_types) return redirect('neutrinus/error/405');
    $order_types_data = $order_types;
    $order_types = $order_types->pluck('name', 'id');

    $general_states = array(1 => 'Elemento habilitado');
    if(auth()->user()->permissionViewDisabledElements->state){ $general_states[2] = 'Elemento deshabilitado';}
    if(auth()->user()->permissionViewHiddenElements->state){ $general_states[3] = 'Elemento oculto';}
    return view('elements.create')->with('materials', $materials)->with('order_types', $order_types)->with('order_types_data', $order_types_data)->with('general_states', $general_states);
  }

  public function store(Request $request, $return=1)
  {
    if(!auth()->user()->permissionCreateElement->state)
    {
      return redirect('projects');
    }
    $elementNumber = Element::max('nro');
    $elementNumber++;
    $request->request->add(['nro' => $elementNumber]);
    $request->request->add(['add' => 0]);
    $request->request->add(['calculated_material_cost' => 0]);
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    if(!isset($request->shared_material)) $request->request->add(['shared_material' => 0]);
    if(!isset($request->sale_price)) $request->request->add(['sale_price' => 0]);
    if(!auth()->user()->permissionEditElementPrice->state)$request->additional_material_cost = 0;
    $request->request->add(['additional_material_cost_date' => Carbon::now()->toDateTimeString()]);

    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'description' => 'string|max:2000|nullable',
      'material_id' => 'required|numeric|min:0',
      'shared_material' => 'required|boolean',
      'order_type_id' => 'required|numeric|min:0',
      'd_ext' => 'required|numeric|min:0',
      'd_int' => 'required|numeric|min:0',
      'side_a' => 'required|numeric|min:0',
      'side_b' => 'required|numeric|min:0',
      'large' => 'required|numeric|min:0',
      'width' => 'required|numeric|min:0',
      'thickness' => 'required|numeric|min:0',
      'quantity_per_manufacturing_series' => 'required|min:0',
      'general_state_id' => 'required|numeric|min:1|max:3',
      'additional_material_cost' => 'required|min:0',
      'additional_material_cost_date' => 'required|date_format:Y-m-d H:i:s',
      'sale_price' => 'required|min:0',
      'author_id' => 'required|numeric|min:0',
      'updater_id' => 'required|numeric|min:0'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre para el elemento'
    ]);

    $element = Element::create($request->except('_token'));
    updateElementMaterialCost($element->id);
    $id = $element->id;
    if($return)
    {
      return redirect("/elements");
    }else{
      return $id;
    }
  }

  public function edit($id)
  {
    if(!auth()->user()->permissionCreateElement->state)
    {
      return redirect('projects');
    }
    $element = Element::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if($element->general_state_id >4 OR !auth()->user()->permissionViewElements->state OR !auth()->user()->permissionCreateElement->state OR ($element->state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($element->state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($element->state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('neutrinus/error/405');
    }

    $materials = visibleMaterials();if(!$materials) return redirect('neutrinus/error/405');
    $materials = $materials->pluck('name', 'id');

    $order_types = visibleOrder_types();if(!$order_types) return redirect('neutrinus/error/405');
    $order_types_data = $order_types;
    $order_types = $order_types->pluck('name', 'id');
    $general_states = array(1 => 'Elemento general habilitado');
    if(auth()->user()->permissionViewDisabledElements->state){ $general_states[2] = 'Elemento general deshabilitado';}
    if(auth()->user()->permissionViewHiddenElements->state){ $general_states[3] = 'Elemento general oculto';}
    if(auth()->user()->permissionViewDeletedElements->state){ $general_states[4] = 'Elemento general eliminado';}
    return view('elements.edit')->withElement($element)->with('materials', $materials)->with('order_types', $order_types)->with('order_types_data', $order_types_data)->with('general_states', $general_states);
  }

  public function update($id, Request $request)
  {
    if(!auth()->user()->permissionCreateElement->state)
    {
      return redirect('projects');
    }
    $element = Element::findOrFail($id);
    if(!auth()->user()->permissionViewElements->state OR !auth()->user()->permissionCreateElement->state OR ($element->general_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($element->general_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($element->general_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect("/projects");
    }
    if(isset($request->additional_material_cost))
    {
      if($element->additional_material_cost != $request->additional_material_cost)
      {
        $request->request->add(['additional_material_cost_date' => Carbon::now()->toDateTimeString()]);
      }
    }
    if(!isset($request->shared_material)) $request->request->add(['shared_material' => 0]);
    $request->request->add(['author_id' => $element->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'description' => 'string|max:2000|nullable',
      'material_id' => 'required|numeric',
      'shared_material' => 'required|boolean',
      'order_type_id' => 'required|numeric',
      'd_ext' => 'required|numeric',
      'd_int' => 'required|numeric',
      'side_a' => 'required|numeric',
      'side_b' => 'required|numeric',
      'large' => 'required|numeric',
      'width' => 'required|numeric',
      'thickness' => 'required|numeric',
      'quantity_per_manufacturing_series' => 'required',
      'general_state_id' => 'required|numeric|min:1|max:4',
      'additional_material_cost' => 'min:0',
      'additional_material_cost_date' => 'date_format:Y-m-d H:i:s',
      'sale_price' => 'min:0',
      'author_id' => 'required|numeric',
      'updater_id' => 'required|numeric'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre para el elemento'
    ]);

    $input = $request->except('_token');

    $element->fill($input)->save();
    updateElementMaterialCost($element->id);
    return redirect($request->prev);
  }

  public function delete(Request $request)
  {
      //Material::find($request->id)->delete();
      if(!auth()->user()->permissionDeleteElement->state)
      {
        return redirect("/projects");
      }
      $element = Element::where('id', $request->id)->update(['general_state_id' => 4]);
      $element = Element::findOrFail($request->id);
      return redirect("/projects");

  }

  public function deepDelete(Request $request)
  {
      if(!auth()->user()->permissionDeleteElement->state)
      {
        return redirect("/projects");
      }
      $element = Element::where('id', $request->id)->update(['general_state_id' => 5]);
      $element = Element::findOrFail($request->id);
      return redirect("/projects");

  }

  public function fetch(Request $request)
  {
   if($request->get('query'))
   {
    $query = $request->get('query');
    if($query!=''){
      if(!(auth()->user()->permissionViewElements->state))
      {$query = '$/&""%¿¿¿284325jlfdgngnggnkndlfvlddofgieorgj';}
      $data = Element::where('name', 'LIKE', "%{$query}%")->orWhere('nro', 'LIKE', "%{$query}%")->get();
      $output = '<ul id="elementSelector" class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $key => $row)
      {
        $showElement = false;
        switch($row->general_state_id){
          case 1:
          $showElement = true;
          break;
          case 3:
            if(auth()->user()->permissionViewHiddenElements->state){
              $showElement = true;
            }
          break;
        }

        if($showElement){
          $output .= '
          <li class="elementSelectable searchElementLi p-1">'.$row->name.' ('.$row->nro.'-'.$row->add.')</li>
          ';
        }else{
          unset($data[$key]);
        }
      }
      $output .= '</ul>';
      if(count($data)==0)
      {
        $output = '';
      }
    }else{
      $output = '';
    }
    echo $output;
   }
  }

  public function saleFetch(Request $request)
  {
   if($request->get('query'))
   {
    $query = $request->get('query');
    if($query!=''){
      if(!(auth()->user()->permissionViewElements->state))
      {$query = '$/&""%¿¿¿284325jlfdgngnggnkndlfvlddofgieorgj';}
      $data = Element::where('name', 'LIKE', "%{$query}%")->orWhere('nro', 'LIKE', "%{$query}%")->get();
      $output = '<ul class="dropdown-menu saleElementSelector" style="display:block; position:relative">';
      foreach($data as $key => $row)
      {
        $showElement = false;
        switch($row->general_state_id){
          case 1:
          $showElement = true;
          break;
          case 3:
            if(auth()->user()->permissionViewHiddenElements->state){
              $showElement = true;
            }
          break;
        }

        if($showElement){
          $output .= '
          <li class="saleElementSelectable saleSearchElementLi p-1" id="saleElement'.$row->id.'">'.$row->name.' ('.$row->nro.'-'.$row->add.')</li>
          ';
        }else{
          unset($data[$key]);
        }
      }
      $output .= '</ul>';
      if(count($data)==0)
      {
        $output = '';
      }
    }else{
      $output = '';
    }
    echo $output;
   }
  }

  public function searchElement(Request $request, $showAll = 1)
  {
   if($request->get('query'))
   {
    $query = $request->get('query');
    if($query!=''){
      $elements = $this->lookForElements($showAll, $query);

      return view('elements')->with('elements', $elements)->withShowAll(1)->withoptionShowAll(0)->withQuery($query);
    }else{return redirect(url()->previous());}
  }else{return redirect(url()->previous());}
  }

  public function storeSupplierCode(Request $request, $return=1)
  {
    if(!auth()->user()->permissionEditSupplier_code->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['state_id' => 1]);
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);

    $validatedData = $request->validate([
      '_token' => 'required',
      'element_id' => 'required|exists:elements,id',
      'supplier_id' => 'required|exists:suppliers,id',
      'state_id' => 'required|numeric|min:1|max:4',
      'description' => 'string|max:2000|nullable',
      'code' => 'required|string',
      'author_id' => 'required|numeric|min:0',
      'updater_id' => 'required|numeric|min:0'
    ],
    [
      'code.required' => 'Es necesario incluir un código.'
    ]);

    $supplier_code = Supplier_code::create($request->except('_token'));
    $id = $supplier_code->id;
    if($return)
    {
      return redirect(url()->previous());
    }else{
      return $id;
    }
  }

  public function deepDeleteSupplierCode(Request $request)
  {
      if(!auth()->user()->permissionEditSupplier_code->state)
      {
        return redirect('neutrinus/error/405');
      }
      $request->request->add(['id' => $request->id]);
      $request->request->add(['_token' => $request->_token]);
      $v = Validator::make($request->all(), [
        '_token' => 'required',
        'id' => 'required|numeric|exists:supplier_codes,id',
      ]);
      if ($v->fails() OR ($request->_token != csrf_token())) {
        return redirect('neutrinus/error/405');
      }else{
        $supplier_code = Supplier_code::where('id', '=', $request->id)->delete();
      }
      return redirect(url()->previous());

  }

}
