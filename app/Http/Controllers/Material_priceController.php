<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Material_price;
use App\Material;
use App\Order_type;
use App\Supplier;
use Carbon\Carbon;

class Material_priceController extends Controller
{
  public function showPrices() {
    if(!auth()->user()->permissionViewMaterialPrices->state)
    {
      return redirect('neutrinus/error/405');
    }
    $material_prices = Material_price::orderBy('material_id', 'ASC')
                                      ->orderBy('order_type_id', 'ASC')
                                      ->orderBy('d_ext', 'DESC')
                                      ->orderBy('d_int', 'DESC')
                                      ->orderBy('side_a', 'DESC')
                                      ->orderBy('side_b', 'DESC')
                                      ->orderBy('width', 'DESC')
                                      ->orderBy('thickness', 'DESC')
                                      ->orderBy('enabled', 'DESC')
                                      ->orderBy('updated_at', 'DESC')
                                      ->paginate(100);
    $outOfDate = Carbon::now()->subMonths(3); //Fecha limite a partir de la cual da alarma por precio viejo
    return view('material_prices')->with('prices', $material_prices)->with('outOfDate', $outOfDate);
  }

  public function create()
  {
    if(!auth()->user()->permissionCreateMaterialPrice->state OR !auth()->user()->permissionViewMaterialPrices->state)
    {
      return redirect('neutrinus/error/405');
    }

    $materials = visibleMaterials();if(!$materials) return redirect('neutrinus/error/405');
    $materials = $materials->pluck('name', 'id');

    $order_types = visibleOrder_types();if(!$order_types) return redirect('neutrinus/error/405');
    $order_types_data = $order_types;
    $order_types = $order_types->pluck('name', 'id');

    $suppliers = visibleSuppliers();if(!$suppliers) return redirect('neutrinus/error/405');
    $suppliers_data = $suppliers;
    $suppliers = $suppliers->pluck('name', 'id');

    return view('material_prices.create')->with('materials', $materials)->with('order_types', $order_types)->with('order_types_data', $order_types_data)->with('suppliers', $suppliers);
  }

  public function store(Request $request, $return=1)
  {
    if(!auth()->user()->permissionCreateMaterialPrice->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['enabled' => 1]);
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);

    $validatedData = $request->validate([
      '_token' => 'required',
      'material_id' => 'required|numeric|min:0',
      'order_type_id' => 'required|numeric|min:0',
      'd_ext' => 'required|numeric|min:0',
      'd_int' => 'required|numeric|min:0',
      'side_a' => 'required|numeric|min:0',
      'side_b' => 'required|numeric|min:0',
      'width' => 'required|numeric|min:0',
      'thickness' => 'required|numeric|min:0',
      'price' => 'required|min:0',
      'enabled' => 'required|boolean',
      'supplier_id' => 'required|min:0',
      'author_id' => 'required|numeric|min:0',
      'updater_id' => 'required|numeric|min:0'
    ]);
    onMaterialCostChanges($request);
    $material_price = Material_price::create($request->except('_token'));
    $id = $material_price->id;
    updateElementsMaterialCost($request->material_id, $request->order_type_id, $request->d_ext, $request->d_int, $request->side_a, $request->side_b, $request->width, $request->thickness, $request->price);
    if($return)
    {
      return redirect("/materialprices");
    }else{
      return $id;
    }
  }

  public function edit($id)
  {
    if(!auth()->user()->permissionCreateMaterialPrice->state)
    {
      return redirect('neutrinus/error/405');
    }
    $material_price = Material_price::findOrFail($id);

    $materials = visibleMaterials();if(!$materials) return redirect('neutrinus/error/405');
    $materials = $materials->pluck('name', 'id');

    $order_types = visibleOrder_types();if(!$order_types) return redirect('neutrinus/error/405');
    $order_types_data = $order_types;
    $order_types = $order_types->pluck('name', 'id');

    $suppliers = visibleSuppliers();if(!$suppliers) return redirect('neutrinus/error/405');
    $suppliers_data = $suppliers;
    $suppliers = $suppliers->pluck('name', 'id');

    return view('material_prices.edit')->with('material_price', $material_price)->with('materials', $materials)->with('order_types', $order_types)->with('order_types_data', $order_types_data)->withSuppliers($suppliers);
  }

  public function update($id, Request $request)
  {
    if(!auth()->user()->permissionCreateMaterialPrice->state)
    {
      return redirect('neutrinus/error/405');
    }
    $material_price = Material_price::findOrFail($id);
    $request->request->add(['enabled' => 1]);
    $request->request->add(['author_id' => $material_price->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'material_id' => 'required|numeric|min:0',
      'order_type_id' => 'required|numeric|min:0',
      'd_ext' => 'required|numeric|min:0',
      'd_int' => 'required|numeric|min:0',
      'side_a' => 'required|numeric|min:0',
      'side_b' => 'required|numeric|min:0',
      'width' => 'required|numeric|min:0',
      'thickness' => 'required|numeric|min:0',
      'price' => 'required|min:0',
      'enabled' => 'required|boolean',
      'supplier_id' => 'required|min:0',
      'author_id' => 'required|numeric|min:0',
      'updater_id' => 'required|numeric|min:0'
    ]);

    $input = $request->except('_token');
    onMaterialCostChanges($request);
    $material_price->fill($input)->save();
    updateElementsMaterialCost($request->material_id, $request->order_type_id, $request->d_ext, $request->d_int, $request->side_a, $request->side_b, $request->width, $request->thickness, $request->price);
    return redirect('materialprices');
  }

  public function delete(Request $request)
  {
      //Material::find($request->id)->delete();
      if(!auth()->user()->permissionCreateMaterialPrice->state)
      {
        return redirect('neutrinus/error/405');
      }
      $material_price_data = Material_price::findOrFail($request->id);
      $material_price = Material_price::findOrFail($request->id)->delete();
      updateElementsMaterialCost($material_price_data->material_id, $material_price_data->order_type_id, $material_price_data->d_ext, $material_price_data->d_int, $material_price_data->side_a, $material_price_data->side_b, $material_price_data->width, $material_price_data->thickness, $material_price_data->price);
      return redirect("/materialprices");

  }

  public function checkLogicPrice(Request $request)
  {
   if($request->get('material_id'))
   {
      $material_id = $request->get('material_id');
      $order_type_id = $request->get('order_type_id');
      $d_ext = $request->get('d_ext');
      $d_int = $request->get('d_int');
      $side_a = $request->get('side_a');
      $side_b = $request->get('side_b');
      $width = $request->get('width');
      $thickness = $request->get('thickness');
      $price = $request->get('price');

      if($d_ext == 0 AND $d_int == 0 AND $side_a == 0 AND $side_b == 0 AND $width == 0 AND $thickness == 0) return response()->json(['success'=>'El precio de material que intenta insertar no es correcto.']);
      $bestMatch = calcMaterialCost($request);
      if($bestMatch)
      {
        if($bestMatch->price > ($price*1.5) OR $bestMatch->price < ($price/1.5))
        {
          return response()->json(['success'=>FALSE]);
        }
      }
   }
   return response()->json(['success'=>TRUE]);
  }

}
