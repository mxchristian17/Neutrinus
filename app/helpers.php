<?php
if (! function_exists('visibleProjects')) {
    function visibleProjects($paginate = false, $paginationNumber = 100, $showAll = true, $showDeleted = false, $pluck = false) {
      if(!auth()->user()->permissionViewProjects->state){
        return false;
      }
      if (!$paginate) $paginationNumber = 100000;
      if($showAll){
        if($showDeleted)
        {
          $projects = App\Project::orderBy('name', 'ASC')->paginate($paginationNumber);
        }else{
          $projects = App\Project::where('state_id', '<>', '4')->orderBy('name', 'ASC')->paginate($paginationNumber);
        }
      }else{
        $projects = App\Project::where('state_id', '=', '1')->orderBy('name', 'ASC')->paginate($paginationNumber);
      }
      foreach ($projects as $project)
      {
        if(($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state))
        {
          $projects = $projects->except($project->id);
        }
      }
      if($pluck) $projects = $projects->pluck('name', 'id');
      return $projects;
    }
}

if (! function_exists('projectIsVisible')) {
  function projectIsVisible($project_id) {
    $project = App\Project::find($project_id);
    if($project)
    {
      if(!(!auth()->user()->permissionViewProjects->state OR ($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state)))
      {
        return $project;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
}

if (! function_exists('visibleSubsets')) {
    function visibleSubsets($project_id, $paginate = false, $paginationNumber = 100, $showAll = true, $showDeleted = false, $pluck = false) {
      if (!$paginate) $paginationNumber = 100000;
      if($showAll){
        if($showDeleted)
        {
          $subsets = App\Subset::where('project_id', $project_id)->orderBy('id', 'ASC')->paginate($paginationNumber);
        }else{
          $subsets = App\Subset::where('project_id', $project_id)->where('state_id', '<>', '4')->orderBy('id', 'ASC')->paginate($paginationNumber);
        }
      }else{
        $subsets = App\Subset::where('project_id', $project_id)->where('state_id', '=', '1')->orderBy('id', 'ASC')->paginate($paginationNumber);
      }
      foreach ($subsets as $subset)
      {
        if(($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state))
        {
          $subsets = $subsets->except($subset->id);
        }
      }
      if($pluck) $subsets = $subsets->pluck('name', 'id');
      return $subsets;
    }
}

if (! function_exists('subsetIsVisible')) {
    function subsetIsVisible($subset_id) {
      $subset = App\Subset::find($subset_id);
      if($subset)
      {
        if(!(($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state)))
        {
          return $subset;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }
}

if (! function_exists('elementIsVisible')) {
  function elementIsVisible($element_id) {
    $element = App\Element::find($element_id);
    if($element)
    {
      if(!(!auth()->user()->permissionViewElements->state OR ($element->general_state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($element->general_state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($element->general_state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state)))
      {
        return $element;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
}

if (! function_exists('visibleSuppliers')) {
    function visibleSuppliers($paginate = false, $paginationNumber = 100, $showAll = true, $showDeleted = false, $pluck = false) {
      if(!auth()->user()->permissionViewSuppliers->state){
        return false;
      }
      if (!$paginate) $paginationNumber = 100000;
      if($showAll){
        if($showDeleted)
        {
          $suppliers = App\Supplier::orderBy('name', 'ASC')->paginate($paginationNumber);
        }else{
          $suppliers = App\Supplier::where('state_id', '<>', '4')->orderBy('name', 'ASC')->paginate($paginationNumber);
        }
      }else{
        $suppliers = App\Supplier::where('state_id', '=', '1')->orderBy('name', 'ASC')->paginate($paginationNumber);
      }
      foreach ($suppliers as $supplier)
      {
        if(($supplier->state_id == 2 AND !auth()->user()->permissionViewDisabledSuppliers->state) OR ($supplier->state_id == 3 AND !auth()->user()->permissionViewHiddenSuppliers->state) OR ($supplier->state_id == 4 AND !auth()->user()->permissionViewDeletedSuppliers->state))
        {
          $suppliers = $suppliers->except($supplier->id);
        }
      }
      if($pluck) $suppliers = $suppliers->pluck('name', 'id');
      return $suppliers;
    }
}

if (! function_exists('visibleClients')) {
    function visibleClients($paginate = false, $paginationNumber = 100, $showAll = true, $showDeleted = false, $pluck = false) {
      if(!auth()->user()->permissionViewClients->state){
        return false;
      }
      if (!$paginate) $paginationNumber = 100000;
      if($showAll){
        if($showDeleted)
        {
          $clients = App\Client::orderBy('name', 'ASC')->paginate($paginationNumber);
        }else{
          $clients = App\Client::where('state_id', '<>', '4')->orderBy('name', 'ASC')->paginate($paginationNumber);
        }
      }else{
        $clients = App\Client::where('state_id', '=', '1')->orderBy('name', 'ASC')->paginate($paginationNumber);
      }
      foreach ($clients as $client)
      {
        if(($client->state_id == 2 AND !auth()->user()->permissionViewDisabledClients->state) OR ($client->state_id == 3 AND !auth()->user()->permissionViewHiddenClients->state) OR ($client->state_id == 4 AND !auth()->user()->permissionViewDeletedClients->state))
        {
          $clients = $clients->except($client->id);
        }
      }
      if($pluck) $clients = $clients->pluck('name', 'id');
      return $clients;
    }
}

if (! function_exists('clientIsVisible')) {
  function clientIsVisible($client_id) {
    $client = App\Client::find($client_id);
    if($client)
    {
      if(!(!auth()->user()->permissionViewClients->state OR ($client->state_id == 2 AND !auth()->user()->permissionViewDisabledClients->state) OR ($client->state_id == 3 AND !auth()->user()->permissionViewHiddenClients->state) OR ($client->state_id == 4 AND !auth()->user()->permissionViewDeletedClients->state)))
      {
        return $client;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
}

if (! function_exists('operationIsVisible')) {
  function operationIsVisible($operation_id) {
    $operation = App\Operation::find($operation_id);
    if($operation)
    {
      if(!(!auth()->user()->permissionViewOperations->state OR ($operation->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state)))
      {
        return $operation;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
}

if (! function_exists('visibleMaterials')) {
    function visibleMaterials() {
      if(!auth()->user()->permissionViewMaterials->state){
        return false;
      }
      $materials = App\Material::all();
      foreach ($materials as $material)
      {
        if(($material->state_id == 2 AND !auth()->user()->permissionViewDisabledMaterials->state) OR ($material->state_id == 3 AND !auth()->user()->permissionViewHiddenMaterials->state) OR ($material->state_id == 4 AND !auth()->user()->permissionViewDeletedMaterials->state))
        {
          $materials = $materials->except($material->id);
        }
      }
      return $materials;
    }
}

if (! function_exists('visibleOrder_types')) {
    function visibleOrder_types() {
      if(!auth()->user()->permissionViewOrder_types->state){
        return false;
      }
      $order_types = App\Order_type::all();
      foreach ($order_types as $order_type)
      {
        if(($order_type->state_id == 2 AND !auth()->user()->permissionViewDisabledOrder_types->state) OR ($order_type->state_id == 3 AND !auth()->user()->permissionViewHiddenOrder_types->state) OR ($order_type->state_id == 4 AND !auth()->user()->permissionViewDeletedOrder_types->state))
        {
          $order_types = $order_types->except($order_type->id);
        }
      }
      return $order_types;
    }
}

if (! function_exists('visibleOperation_names')) {
    function visibleOperation_names() {
      if(!auth()->user()->permissionViewOperations->state){
        return false;
      }
      $operation_names = App\Operation_name::all();
      foreach ($operation_names as $operation_name)
      {
        if(($operation_name->state_id == 2 AND !auth()->user()->permissionViewDisabledOperations->state) OR ($operation_name->state_id == 3 AND !auth()->user()->permissionViewHiddenOperations->state) OR ($operation_name->state_id == 4 AND !auth()->user()->permissionViewDeletedOperations->state))
        {
          $operation_names = $operation_names->except($operation_name->id);
        }
      }
      return $operation_names;
    }
}

if (! function_exists('onMaterialCostChanges'))
{
  function onMaterialCostChanges($request)
  {
    $data = App\Material_price::where([
                                      ['material_id', '=', $request->material_id],
                                      ['order_type_id', '=', $request->order_type_id],
                                      ['d_ext', '=', $request->d_ext],
                                      ['d_int', '=', $request->d_int],
                                      ['side_a', '=', $request->side_a],
                                      ['side_b', '=', $request->side_b],
                                      ['width', '=', $request->width],
                                      ['thickness', '=', $request->thickness],
                                      ['enabled', '=', 1]
                                      ])
                                      ->where('updated_at', '>', Carbon\Carbon::now()->subDays(700)->toDateTimeString())
                                      ->get();
    foreach($data as $key => $material_price)
    {
      foreach($data as $compare_material)
      {
        if( $compare_material->d_ext == $material_price->d_ext AND
            $compare_material->d_int == $material_price->d_int AND
            $compare_material->side_a == $material_price->side_a AND
            $compare_material->side_b == $material_price->side_b AND
            $compare_material->width == $material_price->width AND
            $compare_material->thickness == $material_price->thickness AND
            (Carbon\Carbon::parse($compare_material->updated_at) > Carbon\Carbon::parse($material_price->updated_at)))
        {
          $material_price->enabled = 0;
          $material_price->timestamps = false;
          $material_price->save();
          $material_price->timestamps = true;
          unset($data[$key]);
        }
      }
    }
  }
}

if (! function_exists('calcMaterialCost')) {
    function calcMaterialCost($element) {
      $data = App\Material_price::where([
                                        ['material_id', '=', $element->material_id],
                                        ['order_type_id', '=', $element->order_type_id],
                                        ['d_ext', '<=', ($element->d_ext*1.1)],
                                        ['d_ext', '>=', ($element->d_ext*0.9)],
                                        ['d_int', '<=', ($element->d_int*1.1)],
                                        ['d_int', '>=', ($element->d_int*0.9)],
                                        ['side_a', '<=', ($element->side_a*1.1)],
                                        ['side_a', '>=', ($element->side_a*0.9)],
                                        ['side_b', '<=', ($element->side_b*1.1)],
                                        ['side_b', '>=', ($element->side_b*0.9)],
                                        ['width', '<=', ($element->width*1.1)],
                                        ['width', '>=', ($element->width*0.9)],
                                        ['thickness', '<=', ($element->thickness*1.1)],
                                        ['thickness', '>=', ($element->thickness*0.9)],
                                        ['enabled', '=', 1]
                                        ])
                                        ->where('updated_at', '>', Carbon\Carbon::now()->subDays(700)->toDateTimeString())
                                        ->get();
        $difference=0;
        $min_difference=999999999;
        $bestMatch = FALSE;
        foreach($data as $material_price)
        {
          $difference=$difference+abs($material_price->d_ext - $element->d_ext)+abs($material_price->d_int - $element->d_int)+abs($material_price->side_a - $element->side_a)+abs($material_price->side_b - $element->side_b)+abs($material_price->width - $element->width)+abs($material_price->thickness - $element->thickness);
          if($difference<$min_difference)
          {
            $min_difference = $difference;
            $bestMatch = $material_price;
          }elseif($difference==$min_difference){
            if(Carbon\Carbon::parse($bestMatch->updated_at) < Carbon\Carbon::parse($material_price->updated_at))
            {
              $min_difference = $difference;
              $bestMatch = $material_price;
            }
          }
          $difference = 0;
        }
        return $bestMatch;
    }
}

if(! function_exists('updateElementMaterialCost'))
{
  function updateElementMaterialCost($id)
  {
    $element = App\Element::findOrFail($id);
    $bestMatch = calcMaterialCost($element);
    if($bestMatch)
    {
      $element->calculated_material_cost = $bestMatch->price*$element->weight;
      $element->calculated_material_cost_date = Carbon\Carbon::now()->toDateTimeString();
      $element->save();
      return ($element->calculated_material_cost);
    }else{
      $element->calculated_material_cost = 0;
      $element->calculated_material_cost_date = Carbon\Carbon::now()->toDateTimeString();
      $element->save();
      return 0;
    }
  }
}

if(! function_exists('updateElementsMaterialCost'))
{
  function updateElementsMaterialCost($material_id, $order_type_id, $d_ext, $d_int, $side_a, $side_b, $width, $thickness, $price)
  {
    if($material_id == null AND $order_type_id == null){
      $elements = App\Element::all();
    }elseif ($material_id == null){
      $elements = App\Element::where('order_type_id', '=', $order_type_id)->get();
    }elseif($order_type_id == null){
      $elements = App\Element::where('material_id', '=', $material_id)->get();
    }else{
    $elements = App\Element::where([
                                      ['material_id', '=', $material_id],
                                      ['order_type_id', '=', $order_type_id],
                                      ['d_ext', '<=', ($d_ext*1.1)],
                                      ['d_ext', '>=', ($d_ext*0.9)],
                                      ['d_int', '<=', ($d_int*1.1)],
                                      ['d_int', '>=', ($d_int*0.9)],
                                      ['side_a', '<=', ($side_a*1.1)],
                                      ['side_a', '>=', ($side_a*0.9)],
                                      ['side_b', '<=', ($side_b*1.1)],
                                      ['side_b', '>=', ($side_b*0.9)],
                                      ['width', '<=', ($width*1.1)],
                                      ['width', '>=', ($width*0.9)],
                                      ['thickness', '<=', ($thickness*1.1)],
                                      ['thickness', '>=', ($thickness*0.9)],
                                      ])->get();
    }
    foreach($elements as $element)
    {
      updateElementMaterialCost($element->id);
    }
    return;
  }
}

if(! function_exists('classForGeneralStateTitle'))
{
  function classForGeneralStateTitle($state, $light = false)
  {
    switch(intval($state))
    {
        case 0: if($light){return 'btn-outline-secondary';}else{return 'btn-secondary';}
        break;
        case 1: if($light){return 'btn-outline-success';}else{return 'btn-success';}
        break;
        case 2: if($light){return 'btn-outline-danger';}else{return 'btn-danger';}
        break;
        case 3: if($light){return 'btn-outline-primary';}else{return 'btn-primary';}
        break;
        case 4: if($light){return 'btn-outline-dark';}else{return 'btn-dark';}
        break;
    }
  }
}

if(! function_exists('isUnderCharge'))
{
  function isUnderCharge($superiorUser, $underChargeUser)
  {
    foreach($superiorUser->under_charge as $under_charge)
    {
      if($under_charge->user_under_charge->id == $underChargeUser->id) return true;
    }
    return false;
  }
}

if(! function_exists('isOverCharge'))
{
  function isOverCharge($underChargeUser, $superiorUser)
  {
    foreach($superiorUser->under_charge as $superior)
    {
      if($superior->user_under_charge->id == $underChargeUser->id) return true;
    }
    return false;
  }

  if (! function_exists('visibleSaleStatus')) {
      function visibleSaleStatus() {
        return array(
          '1' => 'Cotizar',
          '2' => 'Cotizado',
          '3' => 'Órden de compras recibida',
          '4' => 'Facturado',
          '5' => 'Listo para entregar',
          '6' => 'Entregado',
          '7' => 'Órden cerrada'
        );
      }
  }
}

if(! function_exists('xor_encrypt')) {
  function xor_encrypt($string) {

      // Let's define our key here
      $key = '$';

      // Our plaintext/ciphertext
      $text = $string;

      // Our output text
      $outText = '';

      // Iterate through each character
      for($i=0; $i<strlen($text); )
      {
          for($j=0; $j<strlen($key); $j++,$i++)
          {
              $outText .= ($text[$i] ^ $key[$j]);
              //echo 'i=' . $i . ', ' . 'j=' . $j . ', ' . $outText{$i} . '<br />'; // For debugging
          }
      }
      return $outText;
  }
}
