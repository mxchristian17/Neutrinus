<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Subset;
use App\Projectelement;
use App\User;
use App\Projecttype;
use Carbon\Carbon;
use App\Recent_project;

class ProjectController extends Controller
{
    public function showProjects($showAll = 0) {
      if($showAll){
        $projects = visibleProjects(true,100,true,true,false);
      }else{
        $projects = visibleProjects(true,100,false,false,false);
      }
      if(!$projects)
      {
        $projects = Project::where('id', 0)->paginate(1);
      }

      return view('projects')->with('projects', $projects)->with('showAll', $showAll);
  	}

    public function showProject($id, $showAll = 0) {
      $subsetByNumber = array();
    	$project = Project::findOrFail($id);
      if(!auth()->user()->permissionViewProjects->state OR ($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state))
      {
        return redirect('neutrinus/error/405');
      }
      if($showAll){
      $subsets = Subset::where('project_id', '=', $id)
    		->orderBy('subset_number', 'ASC')
        ->get();
      }else{
        $subsets = Subset::where([['project_id', '=', $id], ['state_id', '=', '1']])
      		->orderBy('subset_number', 'ASC')
          ->get();
      }

      // PROJECTS FOR COPYTO SELECTOR START

      $copyToProjects = visibleProjects(true,100,true,false,true);
      $copyToProjects->prepend('', 0);

      // PROJECTS FOR COPYTO SELECTOR END


      // RECENT PROJECT VISIT REGISTER START
      Recent_project::where('user_id', auth()->user()->id)->where('project_id', $project->id)->delete();
      $recent_project = new Recent_project;
      $recent_project->user_id = auth()->user()->id;
      $recent_project->project_id = $project->id;
      $recent_project->visited = Carbon::now();
      $recent_project->save();
      // RECENT PROJECT VISIT REGISTER END


    	return view('project')->with('project', $project)->with('subsets', $subsets)->with('showAll', $showAll)->withCopyToProjects($copyToProjects);
    }

    public function showProjectStats($id) {
      $subsetByNumber = array();
    	$project = Project::findOrFail($id);
      if(!auth()->user()->permissionViewOperationPrice->state OR !auth()->user()->permissionViewProjects->state OR ($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state))
      {
        return redirect('neutrinus/error/405');
      }
      $projectDirectCost = 0;
      $projectIndirectCost = 0;
      $elements = collect();
      $elementCount = 0;
      $elementWithDirectCostCount = 0;
      $projectWeight = 0;
      $projectMaterialCost = 0;
      $projectMaterialCostCount = 0;
      $projectAdditionalMaterialCost = 0;
      $projectAdditionalMaterialCostCount = 0;
      foreach($project->projectelements as $projectelement)
      {
        if($projectelement->specific_state_id == 1 OR $projectelement->specific_state_id == 3)
        {
          $projectelement = app('App\Http\Controllers\ProjectelementController')->projectelementDirectCost($projectelement);
          $projectDirectCost = $projectDirectCost + $projectelement->directCost;
          $projectWeight = $projectWeight + $projectelement->element->weight;
          $elementCount ++;
          $materialCost = $projectelement->element->materialCost;
          if($materialCost >0) $projectMaterialCostCount ++;
          $projectelement->matCost = $materialCost[0]*$projectelement->quantity;
          $projectelement->matCostDate = $materialCost[1];
          $projectMaterialCost = $projectMaterialCost + $projectelement->matCost;
          if($projectelement->directCost >0) $elementWithDirectCostCount ++;
          $projectelement->addMatCost = $projectelement->element->additional_material_cost*$projectelement->quantity;
          $projectelement->addMatCostDate = new Carbon($projectelement->element->additional_material_cost_date);
          $projectAdditionalMaterialCost = $projectAdditionalMaterialCost + $projectelement->addMatCost;
          if($projectelement->element->additional_material_cost >0) $projectAdditionalMaterialCostCount ++;
          //var_dump($projectelement);
        }
      }
      $elements = collect($elements);
      $project->weight = $projectWeight;
      $project->elementCount = $elementCount;
      $project->elementWithDirectCostCount = $elementWithDirectCostCount;
      $project->materialCost = $projectMaterialCost;
      $project->materialCostCount = $projectMaterialCostCount;
      $project->additionalMaterialCost = $projectAdditionalMaterialCost;
      $project->additionalMaterialCostCount = $projectAdditionalMaterialCostCount;
      $projectIndirectCost = $projectDirectCost*config('constants.indirectCostFactor');
      $outOfDate = Carbon::now()->subMonths(3); //Fecha limite a partir de la cual da alarma por precio viejo
    	return view('projects.stats')->with('project', $project)->withElements($elements)->withDirectcost($projectDirectCost)->withIndirectcost($projectIndirectCost)->with('outOfDate', $outOfDate);
    }

    public function create()
    {
      if(!auth()->user()->permissionCreateProject->state)
      {
        return redirect('neutrinus/error/405');
      }
      $projecttypes = Projecttype::all();
      return view('projects.create')->with('projecttypes', $projecttypes);
    }

    public function store(Request $request)
    {
      if(!auth()->user()->permissionCreateProject->state)
      {
        return redirect('neutrinus/error/405');
      }
      $request->request->add(['author_id' => auth()->user()->id]);
      $request->request->add(['updater_id' => auth()->user()->id]);
      $validatedData = $request->validate([
        '_token' => 'required',
        'name' => 'required|unique:projects',
        'type' => 'required|numeric|min:0|max:3',
        'state_id' => 'required|numeric|min:1|max:4',
        'author_id' => 'required',
        'updater_id' => 'required'
      ],
      [
        'name.required' => 'Es necesario incluir un nombre',
        'name.unique' => 'Ya existe un proyecto con el nombre que intenta registrar',
        'type.required' => 'Es necesario seleccionar un tipo de proyecto',
        'type.numeric' => 'El tipo de proyecto seleccionado no es valido',
        'type.min' => 'El tipo de proyecto seleccionado no es valido',
        'type.max' => 'El tipo de proyecto seleccionado no es valido'
      ]);
      $project = Project::create($request->except('_token'));
      $id = $project->id;
      return redirect("/project/$id");
    }

    public function edit($id)
    {
      $project = Project::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
      if(!auth()->user()->permissionViewProjects->state OR !auth()->user()->permissionCreateProject->state OR ($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state))
      {
        return redirect('neutrinus/error/405');
      }
      $projecttypes= Projecttype::all()->pluck('name', 'id');
      $general_states = array(1 => 'Proyecto habilitado');
      if(auth()->user()->permissionViewDisabledProjects->state){ $general_states[2] = 'Proyecto deshabilitado';}
      if(auth()->user()->permissionViewHiddenProjects->state){ $general_states[3] = 'Proyecto oculto';}
      if(auth()->user()->permissionViewDeletedProjects->state AND auth()->user()->permissionDeleteProject->state){ $general_states[4] = 'Proyecto eliminado';}
      return view('projects.edit')->withProject($project)->with('general_states', $general_states)->with('projecttypes', $projecttypes);
    }

    public function update($id, Request $request)
    {
      $project = Project::findOrFail($id);
      if(!auth()->user()->permissionViewProjects->state OR !auth()->user()->permissionCreateProject->state OR ($project->state_id == 2 AND !auth()->user()->permissionViewDisabledProjects->state) OR ($project->state_id == 3 AND !auth()->user()->permissionViewHiddenProjects->state) OR ($project->state_id == 4 AND !auth()->user()->permissionViewDeletedProjects->state))
      {
        return redirect('neutrinus/error/405');
      }
      if($request->state_id==4 AND !auth()->user()->permissionDeleteProject->state)
      {
        return redirect('neutrinus/error/405');
      }
      $request->request->add(['author_id' => $project->author_id]);
      $request->request->add(['updater_id' => auth()->user()->id]);
      $validatedData = $request->validate([
        '_token' => 'required',
        'name' => 'required',
        'type' => 'required|min:1|max:3',
        'state_id' => 'required|numeric',
        'author_id' => 'required',
        'updater_id' => 'required'
      ],
      [
        'name.required' => 'Es necesario incluir un nombre',
        'type.required' => 'Es necesario incluir un tipo que defina al proyecto',
        'type.max' => 'El tipo de proyecto no es correcto',
        'type.min' => 'El tipo de proyecto no es correcto',
        'state_id.required' => 'Es necesario incluir un estado de project',
        'state_id.numeric' => 'El estado de project no es válido'
      ]);

      $input = $request->except('_token');

      $project->fill($input)->save();

      return redirect("project/$request->id");
    }

    public function delete(Request $request)
    {
        if(!auth()->user()->permissionDeleteProject->state)
        {
          return redirect('neutrinus/error/405');
        }
        //Material::find($request->id)->delete();
        Project::where('id', $request->id)->update(['state_id' => 4]);

        return redirect('/projects');

    }

    public function deleteForEver(Request $request)
    {
        if(!auth()->user()->permissionDeleteProject->state)
        {
          return redirect('neutrinus/error/405');
        }
        Projectelement::where('project_id', '=', $request->id)->delete();
        Recent_project::where('project_id', '=', $request->id)->delete();
        Subset::where('project_id', '=', $request->id)->delete();
        Project::findOrFail($request->id)->delete();
        return redirect('/projects');

    }

/*    public function getProjectelementsData($project_id) {
      $user = User::all();
  		$project = Project::find($project_id);
      $elements = Projectelement::where('project_id', '=', $project_id)
        ->orderBy('subset_id', 'ASC')
        ->orderBy('part')
        ->orderBy('subpart')
        ->orderBy('version')
        ->get();
      $elements = DB::table('projectelements')
      ->join('elements', 'projectelements.element_id', '=', 'elements.id')
      ->where('project_id', '=', $project_id)->orderBy('subset_id', 'ASC')->orderBy('part')->orderBy('subpart')->orderBy('version')
      ->get();
      $subsets = Subset::where('project_id', '=', $project_id)->get();
  		return view('project')->with('elements', $elements)->with('project', $project)->with('subsets', $subsets)->with('user', $user);
  	}
*/

    function completeSubsetsSelector(Request $request)
    {
      $validatedData = $request->validate([
        '_token' => 'required',
        'id' => 'required|numeric|exists:projects'
      ]);
      $subsets = visibleSubsets($request->id,false,100,true,false,false);
      $data = '';
      foreach($subsets as $subset)
      {
        $data .= '<option value="'.$subset->id.'">'.$subset->name.'</option>';
      }
      return $data;
    }
}
