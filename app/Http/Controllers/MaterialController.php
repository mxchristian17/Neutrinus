<?php

namespace App\Http\Controllers;
use App\Material;
use App\User;

use Illuminate\Http\Request;

class MaterialController extends Controller
{

  public function showMaterials($showAll = 0) {
    if(auth()->user()->permissionViewMaterials->state)
    {
      if($showAll){
        $materials = Material::orderBy('name', 'ASC')->paginate(50);
      }else{
        $materials = Material::where('state_id', '=', '1')->orderBy('name', 'ASC')->paginate(50);
      }
    }else
    {
      return redirect('neutrinus/error/405');
    }

    return view('materials')->with('materials', $materials)->with('showAll', $showAll);
  }

  public function create()
  {
    if(!auth()->user()->permissionCreateMaterial->state)
    {
      return redirect('neutrinus/error/405');
    }
    return view('materials.create');
  }

  public function store(Request $request)
  {
    if(!auth()->user()->permissionCreateMaterial->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'initials' => 'required|max:8',
      'specific_weight' => 'required|numeric|min:0',
      'state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'initials.required' => 'Es necesario incluir una sigla que definal al material',
      'initials.max' => 'Es necesario que la sigla que define al material tenga un máximo de 8 caracteres',
      'specific_weight.required' => 'Es necesario incluir un valor de peso específico',
      'specific_weight.numeric' => 'El valor de peso específico debe ser numérico',
      'specific_weight.min' => 'El valor de peso específico debe ser mayor a 0'
    ]);
    Material::create($request->except('_token'));

    return redirect('/materials');
  }

  public function edit($id)
  {
    $material = Material::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewMaterials->state OR !auth()->user()->permissionCreateMaterial->state OR ($material->state_id == 2 AND !auth()->user()->permissionViewDisabledMaterials->state) OR ($material->state_id == 3 AND !auth()->user()->permissionViewHiddenMaterials->state) OR ($material->state_id == 4 AND !auth()->user()->permissionViewDeletedMaterials->state))
    {
      return redirect('neutrinus/error/405');
    }
    $general_states = array(1 => 'Material habilitado');
    if(auth()->user()->permissionViewDisabledMaterials->state){ $general_states[2] = 'Material deshabilitado';}
    if(auth()->user()->permissionViewHiddenMaterials->state){ $general_states[3] = 'Material oculto';}
    if(auth()->user()->permissionViewDeletedMaterials->state){ $general_states[4] = 'Material eliminado';}
    return view('materials.edit')->withMaterial($material)->with('general_states', $general_states);
  }

  public function update($id, Request $request)
  {
    $material = Material::findOrFail($id);
    if(!auth()->user()->permissionViewMaterials->state OR !auth()->user()->permissionCreateMaterial->state OR ($material->state_id == 2 AND !auth()->user()->permissionViewDisabledMaterials->state) OR ($material->state_id == 3 AND !auth()->user()->permissionViewHiddenMaterials->state) OR ($material->state_id == 4 AND !auth()->user()->permissionViewDeletedMaterials->state))
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['author_id' => $material->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'initials' => 'required|max:8',
      'specific_weight' => 'required|numeric|min:0',
      'state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'initials.required' => 'Es necesario incluir una sigla que definal al material',
      'initials.max' => 'Es necesario que la sigla que define al material tenga un máximo de 8 caracteres',
      'specific_weight.required' => 'Es necesario incluir un valor de peso específico',
      'specific_weight.numeric' => 'El valor de peso específico debe ser numérico',
      'specific_weight.min' => 'El valor de peso específico debe ser mayor a 0',
      'state_id.required' => 'Es necesario incluir un estado de material',
      'state_id.numeric' => 'El estado de material no es válido'
    ]);

    $input = $request->except('_token');

    $material->fill($input)->save();
    updateElementsMaterialCost($id, null, null, null, null, null, null, null, null);
    return redirect('materials');
  }

  public function delete(Request $request)
  {
      //Material::find($request->id)->delete();
      if(!auth()->user()->permissionDeleteMaterial->state)
      {
        return redirect('neutrinus/error/405');
      }
      Material::where('id', $request->id)->update(['state_id' => 4]);
      return redirect('/materials');

  }

  public function deleteForEver(Request $request)
  {
      if(!auth()->user()->permissionDeleteMaterial->state)
      {
        return redirect('neutrinus/error/405');
      }
      Material::findOrFail($request->id)->delete();
      updateElementsMaterialCost(null, null, null, null, null, null, null, null, null);
      return redirect('/materials');

  }

}
