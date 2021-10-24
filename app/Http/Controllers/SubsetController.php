<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Project;
use App\Subset;
use App\Projectelement;
use Session;

class SubsetController extends Controller
{

  public function create($project_id)
  {
    if(auth()->user()->permissionCreateSubset->state)
    {
      $states = array(1 => 'Subconjunto habilitado');
      if(auth()->user()->permissionViewDisabledSubsets->state){
        $states[2] = 'Subconjunto deshabilitado';
      }
      if(auth()->user()->permissionViewHiddenSubsets->state){
        $states[3] = 'Subconjunto oculto';
      }

      $subsetNumber = Subset::where('project_id', '=', $project_id)->max('subset_number');
      $subsetNumber++;
      $project = Project::findOrFail($project_id);
      return view('subsets.create')->with('project', $project)->with('subset', $subsetNumber)->with('states', $states);
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function store(Request $request)
  {
    if(auth()->user()->permissionCreateSubset->state)
    {
      $validatedData = $request->validate([
        '_token' => 'required',
        'name' => 'required',
        'subset_number' => 'required|numeric',
        'project_id' => 'required|numeric',
        'state_id' => 'required|numeric|min:1|max:3',
        'author_id' => 'required|numeric',
        'updater_id' => 'required|numeric'
      ],
      [
        'name.required' => 'Es necesario incluir un nombre para el subconjunto',
        'state_id.required' => 'El estado del subconjunto está mal definido',
        'state_id.numeric' => 'El estado del subconjunto está mal definido',
        'state_id.min' => 'El estado del subconjunto está fuera del rango aceptable',
        'state_id.max' => 'El estado del subconjunto está fuera del rango aceptable'
      ]);

      $subset = Subset::create($request->except('_token'));
      return redirect("/project/$subset->project_id");
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function edit($id)
  {
    $subset = Subset::findOrFail($id);
    if(!auth()->user()->permissionCreateSubset->state OR ($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state))
    {
      return redirect('neutrinus/error/405');
    }
    $general_states = array(1 => 'Subconjunto habilitado');
    if(auth()->user()->permissionViewDisabledSubsets->state){ $general_states[2] = 'Subconjunto deshabilitado';}
    if(auth()->user()->permissionViewHiddenSubsets->state){ $general_states[3] = 'Subconjunto oculto';}
    if(auth()->user()->permissionViewDeletedSubsets->state AND auth()->user()->permissionDeleteSubset->state){ $general_states[4] = 'Subconjunto eliminado';}
    return view('subsets.edit')->withSubset($subset)->with('general_states', $general_states);
  }

  public function update($id, Request $request)
  {
    $subset = Subset::findOrFail($id);
    if(!auth()->user()->permissionCreateSubset->state OR ($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledSubsets->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenSubsets->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedSubsets->state))
    {
      return redirect('neutrinus/error/405');
    }
    if($request->state_id==4 AND !auth()->user()->permissionDeleteSubset->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['author_id' => $subset->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'state_id' => 'required|numeric',
      'author_id' => 'required',
      'updater_id' => 'required'
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'state_id.required' => 'Es necesario incluir un estado de subset',
      'state_id.numeric' => 'El estado de subset no es válido'
    ]);

    $input = $request->except('_token');

    $subset->fill($input)->save();

    return redirect("project/".$subset->project->id);
  }

  public function delete(Request $request)
  {
      if(!auth()->user()->permissionDeleteSubset->state)
      {
        return redirect('neutrinus/error/405');
      }
      //Material::find($request->id)->delete();
      $subset = Subset::findOrFail($request->id);
      Subset::where('id', $request->id)->update(['state_id' => 4]);

      return redirect('/project/'.$subset->project->id);

  }

  public function deleteForEver(Request $request)
  {
      if(!auth()->user()->permissionDeleteSubset->state)
      {
        return redirect('neutrinus/error/405');
      }
      $subset = Subset::findOrFail($request->id);
      $subset->delete();
      Projectelement::where('subset_id', '=', $request->id)->delete();
      return redirect('/project/'.$subset->project->id);

  }

  public function copyTo(Request $request)
  {
    $request->request->add(['author_id' => auth()->user()->id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    $validatedData = $request->validate([
      '_token' => 'required',
      'project_id' => 'required|numeric|exists:projects,id',
      'id' => 'required|numeric|exists:subsets',
      'author_id' => 'required',
      'updater_id' => 'required'
    ]);

    if(!auth()->user()->permissionCreateSubset->state)
    {
      return redirect('neutrinus/error/405');
    }
    $project_id = $request->project_id;
    $subset = Subset::findOrFail($request->id);
    $newProject = projectIsVisible($project_id);
    if(!auth()->user()->permissionCreateSubset->state OR ($subset->state_id == 2 AND !auth()->user()->permissionViewDisabledElements->state) OR ($subset->state_id == 3 AND !auth()->user()->permissionViewHiddenElements->state) OR ($subset->state_id == 4 AND !auth()->user()->permissionViewDeletedElements->state))
    {
      return redirect('neutrinus/error/405');
    }
    if($request->project_id=='' OR !$newProject){
      return redirect('neutrinus/error/405');
    }

    $newSubset = $subset->replicate();
    $subsetNumber = Subset::where('project_id', '=', $project_id)->max('subset_number');
    $subsetNumber++;
    $newSubset->project_id = $project_id;
    $newSubset->subset_number = $subsetNumber;
    $newSubset->push();

    $projectelements = Projectelement::where('subset_id', $subset->id)->get();
    foreach($projectelements as $projectelement)
    {
      $newProjectelement = $projectelement->replicate();
      $newProjectelement->project_id = $project_id;
      $newProjectelement->subset_id = $newSubset->id;
      $newProjectelement->push();
    }

    Session::flash('message.level', 'success');
    Session::flash('status', 'La copia del subconjunto '.$newSubset->name.' ha sido generada con exito al proyecto '.$newProject->name.'. Se han copiado '.count($projectelements).' elementos.');
    return redirect(url()->previous());
  }

}
