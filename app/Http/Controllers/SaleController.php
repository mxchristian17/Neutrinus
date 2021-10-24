<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Sale_item;
use App\Currency;
use App\Project;
use App\Subset;
use App\Element;
use App\Projectelement;
use App\Todo;

class SaleController extends Controller
{
  public function showSales() {

    if(auth()->user()->permissionViewSales->state AND
    auth()->user()->permissionViewClients->state)
    {
        $sales = Sale::orderBy('created_at', 'ASC')->paginate(100);
    }else
    {
      return redirect('neutrinus/error/405');
    }

    return view('sales')->withSales($sales);
  }

  public function showSale($id) {
    if(!auth()->user()->permissionViewSales->state OR
    !auth()->user()->permissionViewProjects->state OR
    !auth()->user()->permissionViewElements->state OR
    !auth()->user()->permissionViewClients->state){ return redirect('neutrinus/error/405'); }
    $sale = Sale::findOrFail($id);

    $sale->quotedValue = 0;
    $sale->discountVal = 0;
    $sale->ivaTaxVal = 0;
    $sale->otherTaxVal = 0;
    $sale->totalVal = 0;

    foreach($sale->items as $item) {
      if($item->project_id) {
        $project = Project::find($item->project_id);
        $item->project = $project;
      }
      if($item->subset_id) {
        $subset = Subset::find($item->subset_id);
        $item->subset = $subset;
      }
      if($item->element_id) {
        $element = Element::find($item->element_id);
        $item->element = $element;
      }

      $item->discountVal = ($item->quotedValue * $item->discount / 100);
      $item->taxVal = (($item->iva_tax_percentaje + $item->other_taxes_percentaje) * ($item->quotedValue - $item->discountVal) / 100);
      $item->totalVal = ($item->quotedValue-$item->discountVal+$item->taxVal)*$item->quantity;

      $sale->quotedValue += $item->quotedValue * $item->quantity;
      $sale->discountVal += $item->discountVal * $item->quantity;
      $sale->ivaTaxVal += ($item->iva_tax_percentaje * ($item->quotedValue - $item->discountVal) / 100) * $item->quantity;
      $sale->otherTaxVal += ($item->other_taxes_percentaje * ($item->quotedValue - $item->discountVal) / 100) * $item->quantity;
      $sale->otherTaxVal += $item->otherTaxVal * $item->quantity;
      $sale->totalVal += $item->totalVal;

      $item->quotedValue = number_format($item->quotedValue, 2 , ',', '.');
      $item->discountVal = number_format($item->discountVal, 2 , ',', '.');
      $item->taxVal = number_format($item->taxVal, 2 , ',', '.');
      $item->totalVal = number_format($item->totalVal, 2 , ',', '.');
    }

    $sale->totalVal += $sale->perceptions;

    $sale->quotedValue = number_format($sale->quotedValue, 2 , ',', '.');
    $sale->discountVal = number_format($sale->discountVal, 2 , ',', '.');
    $sale->ivaTaxVal = number_format($sale->ivaTaxVal, 2 , ',', '.');
    $sale->otherTaxVal = number_format($sale->otherTaxVal, 2 , ',', '.');
    $sale->perceptions = number_format($sale->perceptions, 2 , ',', '.');
    $sale->totalVal = number_format($sale->totalVal, 2 , ',', '.');

    $todo = Todo::where('sale_id', $sale->id)->get();

    return view("sale")->withSale($sale)->withTodo($todo);
  }

  public function create() {
    if(!auth()->user()->permissionCreateSale->state OR
    !auth()->user()->permissionViewProjects->state OR
    !auth()->user()->permissionViewElements->state OR
    !auth()->user()->permissionViewClients->state) return redirect('neutrinus/error/405');
    $client_id = visibleClients(false, 1, true, false, true);
    $currencies = Currency::all()->pluck('name', 'id');
    $status = visibleSaleStatus();
    $project_id = visibleProjects(false,1,1,false,true);
    $project_id->prepend('', 0);
    $states = array(1 => 'Proceso de venta habilitado');
    if(auth()->user()->permissionViewDisabledSales->state){
      $states[2] = 'Proceso de venta deshabilitado';
    }
    if(auth()->user()->permissionViewHiddenSales->state){
      $states[3] = 'Proceso de venta oculto';
    }
    return view('sales.create')->with('client_id', $client_id)->withCurrencies($currencies)->withStatus($status)->with('project_id', $project_id)->withStates($states);
  }

  public function store(Request $request) {
    if(!auth()->user()->permissionCreateSale->state OR
    !auth()->user()->permissionViewProjects->state OR
    !auth()->user()->permissionViewElements->state OR
    !auth()->user()->permissionViewClients->state) return redirect('neutrinus/error/405');
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'order_number' => 'string|nullable|max:50',
      'client_id' => 'required|numeric|exists:clients,id',
      'observations' => 'string|nullable|max:1000',
      'work_order_emitter_id' => 'numeric|nullable|exists:users,id',
      'work_order_observations' => 'string|nullable|max:1000',
      'status' => 'required|numeric|min:0|max:7',
      'currency_id' => 'required|numeric|exists:currencies,id',
      'bill_number' => 'string|nullable|max:100',
      'retentions' => 'numeric|nullable|min:0',
      'perceptions' => 'numeric|nullable|min:0',
      'discount' => 'numeric|nullable|min:0|max:100',
      'requested_delivery_date' => 'required|date_format:Y-m-d\TH:i',
      'quoted_date' => 'date_format:Y-m-d\TH:i|nullable',
      'purchase_order_reception_date' => 'date_format:Y-m-d\TH:i|nullable',
      'ready_to_deliver_date' => 'date_format:Y-m-d\TH:i|nullable',
      'delivered_date' => 'date_format:Y-m-d\TH:i|nullable',
      'scheduled_delivery_date' => 'date_format:Y-m-d\TH:i|nullable',
      'state_id' => 'required|numeric|min:1|max:3',
      'author_id' => 'required|numeric',
      'updater_id' => 'required|numeric',

      'project_project_id.*' => 'numeric|nullable',
      'project_quantity.*' => 'numeric|nullable|min:0',
      'project_serial_number.*' => 'numeric|nullable|min:0',
      'project_observations.*' => 'string|nullable|max:1000',
      'project_quotedValue.*' => 'numeric|nullable|min:0',
      'project_discount.*' => 'numeric|nullable|min:0|max:100',
      'project_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'project_other_taxes_percentaje.*' => 'numeric|nullable|min:0',

      'subset_project_id.*' => 'numeric|nullable',
      'subset_subset_id.*' => 'numeric|nullable',
      'subset_quantity.*' => 'numeric|nullable|min:0',
      'subset_serial_number.*' => 'numeric|nullable|min:0',
      'subset_observations.*' => 'string|nullable|max:1000',
      'subset_quotedValue.*' => 'numeric|nullable|min:0',
      'subset_discount.*' => 'numeric|nullable|min:0|max:100',
      'subset_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'subset_other_taxes_percentaje.*' => 'numeric|nullable|min:0',

      'element_element_id.*' => 'numeric|nullable',
      'element_quantity.*' => 'numeric|nullable|min:0',
      'element_serial_number.*' => 'numeric|nullable|min:0',
      'element_observations.*' => 'string|nullable|max:1000',
      'element_quotedValue.*' => 'numeric|nullable|min:0',
      'element_discount.*' => 'numeric|nullable|min:0|max:100',
      'element_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'element_other_taxes_percentaje.*' => 'numeric|nullable|min:0'

    ],
    [
    ]);

    $sale = Sale::create($request->except('_token'));

    $createTodo = false;
    if($sale->status > 2 and $sale->status <6) $createTodo = true;

    if (is_array($request->project_project_id))
    {
      foreach($request->project_project_id as $key => $project)
      {
        if(!is_null($project))
        {
          if(projectIsVisible($project)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->project_id = $project;
            $sale_item->observations = $request->project_observations[$key];
            $sale_item->quotedValue = $request->project_quotedValue[$key];
            $sale_item->discount = $request->project_discount[$key];
            $sale_item->iva_tax_percentaje = $request->project_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->project_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->project_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->project_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $elements = Projectelement::where('project_id', $project)->get();
              foreach($elements as $element)
              {
                if(($element->specific_state_id == 1 or $element->specific_state_id == 3) AND ($element->subset->state_id == 1 or $element->subset->state_id == 3))
                {
                  for($i=0;$i<$element->quantity;$i++)
                  {
                    $todo = new Todo;
                    $todo->sale_id = $sale->id;
                    $todo->sale_item_id = $sale_item->id;
                    $todo->element_id = $element->element->id;
                    $todo->state = 1;
                    $todo->save();
                  }
                }
              }
            }
          }
        }
      }
    }

    if (is_array($request->subset_subset_id))
    {
      foreach($request->subset_subset_id as $key => $subset)
      {
        if(!is_null($subset))
        {
          if(subsetIsVisible($subset)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->subset_id = $subset;
            $sale_item->observations = $request->subset_observations[$key];
            $sale_item->quotedValue = $request->subset_quotedValue[$key];
            $sale_item->discount = $request->subset_discount[$key];
            $sale_item->iva_tax_percentaje = $request->subset_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->subset_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->subset_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->subset_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $elements = Projectelement::where('subset_id', $subset)->get();
              foreach($elements as $element)
              {
                if($element->specific_state_id == 1 or $element->specific_state_id == 3)
                {
                  for($i=0;$i<$element->quantity;$i++)
                  {
                    $todo = new Todo;
                    $todo->sale_id = $sale->id;
                    $todo->sale_item_id = $sale_item->id;
                    $todo->element_id = $element->element->id;
                    $todo->state = 1;
                    $todo->save();
                  }
                }
              }
            }
          }
        }
      }
    }

    if (is_array($request->element_element_id))
    {
      foreach($request->element_element_id as $key => $element)
      {
        if(!is_null($element))
        {
          if(elementIsVisible($element)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->element_id = $element;
            $sale_item->observations = $request->element_observations[$key];
            $sale_item->quotedValue = $request->element_quotedValue[$key];
            $sale_item->discount = $request->element_discount[$key];
            $sale_item->iva_tax_percentaje = $request->element_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->element_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->element_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->element_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $element = Projectelement::where('element_id', $element)->first();
              $todo = new Todo;
              $todo->sale_id = $sale->id;
              $todo->sale_item_id = $sale_item->id;
              $todo->element_id = $element->element->id;
              $todo->state = 1;
              $todo->save();
            }
          }
        }
      }
    }

    return redirect("/sale/$sale->id");


  }

  public function edit($id) {
    $sale = Sale::findOrFail($id);
    if(!auth()->user()->permissionCreateSale->state OR
    !auth()->user()->permissionViewProjects->state OR
    !auth()->user()->permissionViewElements->state OR
    !auth()->user()->permissionViewClients->state) return redirect('neutrinus/error/405');
    $client_id = visibleClients(false, 1, true, false, true);
    $currencies = Currency::all()->pluck('name', 'id');
    $status = visibleSaleStatus();
    $project_id = visibleProjects(false,1,1,false,true);
    $project_id->prepend('', 0);
    $states = array(1 => 'Proceso de venta habilitado');
    if(auth()->user()->permissionViewDisabledSales->state){
      $states[2] = 'Proceso de venta deshabilitado';
    }
    if(auth()->user()->permissionViewHiddenSales->state){
      $states[3] = 'Proceso de venta oculto';
    }
    if(auth()->user()->permissionViewDeletedSales->state){
      $states[4] = 'Proceso de venta eliminado';
    }
    return view('sales.edit')->withSale($sale)->with('client_id', $client_id)->withCurrencies($currencies)->withStatus($status)->with('project_id', $project_id)->withStates($states);
  }

  public function update($id, Request $request) {
    if(!auth()->user()->permissionCreateSale->state OR
    !auth()->user()->permissionViewProjects->state OR
    !auth()->user()->permissionViewElements->state OR
    !auth()->user()->permissionViewClients->state) return redirect('neutrinus/error/405');
    $sale = Sale::findOrFail($id);
    if(!auth()->user()->permissionViewSales->state OR !auth()->user()->permissionCreateSale->state OR ($sale->state_id == 2 AND !auth()->user()->permissionViewDisabledSales->state) OR ($sale->state_id == 3 AND !auth()->user()->permissionViewHiddenSales->state) OR ($sale->state_id == 4 AND !auth()->user()->permissionViewDeletedSales->state))
    {
      return redirect("/sales");
    }
    $request->request->add(['author_id' => $sale->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'order_number' => 'string|nullable|max:50',
      'client_id' => 'required|numeric|exists:clients,id',
      'observations' => 'string|nullable|max:1000',
      'work_order_emitter_id' => 'numeric|nullable|exists:users,id',
      'work_order_observations' => 'string|nullable|max:1000',
      'status' => 'required|numeric|min:0|max:7',
      'currency_id' => 'required|numeric|exists:currencies,id',
      'bill_number' => 'string|nullable|max:100',
      'retentions' => 'numeric|nullable|min:0',
      'perceptions' => 'numeric|nullable|min:0',
      'discount' => 'numeric|nullable|min:0|max:100',
      'requested_delivery_date' => 'required|date_format:Y-m-d\TH:i',
      'quoted_date' => 'date_format:Y-m-d\TH:i|nullable',
      'purchase_order_reception_date' => 'date_format:Y-m-d\TH:i|nullable',
      'ready_to_deliver_date' => 'date_format:Y-m-d\TH:i|nullable',
      'delivered_date' => 'date_format:Y-m-d\TH:i|nullable',
      'scheduled_delivery_date' => 'date_format:Y-m-d\TH:i|nullable',
      'state_id' => 'required|numeric|min:1|max:3',
      'author_id' => 'required|numeric',
      'updater_id' => 'required|numeric',

      'project_project_id.*' => 'numeric|nullable',
      'project_quantity.*' => 'numeric|nullable|min:0',
      'project_serial_number.*' => 'numeric|nullable|min:0',
      'project_observations.*' => 'string|nullable|max:1000',
      'project_quotedValue.*' => 'numeric|nullable|min:0',
      'project_discount.*' => 'numeric|nullable|min:0|max:100',
      'project_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'project_other_taxes_percentaje.*' => 'numeric|nullable|min:0',

      'subset_project_id.*' => 'numeric|nullable',
      'subset_subset_id.*' => 'numeric|nullable',
      'subset_quantity.*' => 'numeric|nullable|min:0',
      'subset_serial_number.*' => 'numeric|nullable|min:0',
      'subset_observations.*' => 'string|nullable|max:1000',
      'subset_quotedValue.*' => 'numeric|nullable|min:0',
      'subset_discount.*' => 'numeric|nullable|min:0|max:100',
      'subset_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'subset_other_taxes_percentaje.*' => 'numeric|nullable|min:0',

      'element_element_id.*' => 'numeric|nullable',
      'element_quantity.*' => 'numeric|nullable|min:0',
      'element_serial_number.*' => 'numeric|nullable|min:0',
      'element_observations.*' => 'string|nullable|max:1000',
      'element_quotedValue.*' => 'numeric|nullable|min:0',
      'element_discount.*' => 'numeric|nullable|min:0|max:100',
      'element_iva_tax_percentaje.*' => 'numeric|nullable|min:0',
      'element_other_taxes_percentaje.*' => 'numeric|nullable|min:0'

    ],
    [
    ]);

    $input = $request->except('_token');
    $sale->fill($input)->save();
    Sale_item::where('sale_id', $sale->id)->delete();
    Todo::where('sale_id', $sale->id)->delete();
    $createTodo = false;
    if($sale->status > 2 and $sale->status <6) $createTodo = true;

    if (is_array($request->project_project_id))
    {
      foreach($request->project_project_id as $key => $project)
      {
        if(!is_null($project))
        {
          if(projectIsVisible($project)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->project_id = $project;
            $sale_item->observations = $request->project_observations[$key];
            $sale_item->quotedValue = $request->project_quotedValue[$key];
            $sale_item->discount = $request->project_discount[$key];
            $sale_item->iva_tax_percentaje = $request->project_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->project_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->project_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->project_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $elements = Projectelement::where('project_id', $project)->get();
              foreach($elements as $element)
              {
                if(($element->specific_state_id == 1 or $element->specific_state_id == 3) AND ($element->subset->state_id == 1 or $element->subset->state_id == 3))
                {
                  for($i=0;$i<$element->quantity;$i++)
                  {
                    $todo = new Todo;
                    $todo->sale_id = $sale->id;
                    $todo->sale_item_id = $sale_item->id;
                    $todo->element_id = $element->element->id;
                    $todo->state = 1;
                    $todo->save();
                  }
                }
              }
            }
          }
        }
      }
    }

    if (is_array($request->subset_subset_id))
    {
      foreach($request->subset_subset_id as $key => $subset)
      {
        if(!is_null($subset))
        {
          if(subsetIsVisible($subset)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->subset_id = $subset;
            $sale_item->observations = $request->subset_observations[$key];
            $sale_item->quotedValue = $request->subset_quotedValue[$key];
            $sale_item->discount = $request->subset_discount[$key];
            $sale_item->iva_tax_percentaje = $request->subset_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->subset_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->subset_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->subset_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $elements = Projectelement::where('subset_id', $subset)->get();
              foreach($elements as $element)
              {
                if($element->specific_state_id == 1 or $element->specific_state_id == 3)
                {
                  for($i=0;$i<$element->quantity;$i++)
                  {
                    $todo = new Todo;
                    $todo->sale_id = $sale->id;
                    $todo->sale_item_id = $sale_item->id;
                    $todo->element_id = $element->element->id;
                    $todo->state = 1;
                    $todo->save();
                  }
                }
              }
            }
          }
        }
      }
    }

    if (is_array($request->element_element_id))
    {
      foreach($request->element_element_id as $key => $element)
      {
        if(!is_null($element))
        {
          if(elementIsVisible($element)){
            $sale_item = new Sale_item;
            $sale_item->sale_id = $sale->id;
            $sale_item->element_id = $element;
            $sale_item->observations = $request->element_observations[$key];
            $sale_item->quotedValue = $request->element_quotedValue[$key];
            $sale_item->discount = $request->element_discount[$key];
            $sale_item->iva_tax_percentaje = $request->element_iva_tax_percentaje[$key];
            $sale_item->other_taxes_percentaje = $request->element_other_taxes_percentaje[$key];
            $sale_item->quantity = $request->element_quantity[$key] ?? 1;
            $sale_item->serial_number = $request->element_serial_number[$key];
            $sale_item->author_id = auth()->user()->id;
            $sale_item->updater_id = auth()->user()->id;
            $sale_item->save();
            if($sale and $sale_item and $createTodo)
            {
              $element = Element::find($element)->first();
              $todo = new Todo;
              $todo->sale_id = $sale->id;
              $todo->sale_item_id = $sale_item->id;
              $todo->element_id = $element->id;
              $todo->state = 1;
              $todo->save();
            }
          }
        }
      }
    }

    return redirect("/sale/$sale->id");


  }

}
