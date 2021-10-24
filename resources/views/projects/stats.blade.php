@extends('layouts.app')
@section('pageTitle'){{$project->name}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Project.js')}}"></script>
	<script type="application/javascript" src="{{ asset('js/Projectelement.js')}}"></script>
	<script src="{{ asset('js/Chart.bundle.js')}}" charset="utf-8"></script>
	<script>
	$(document).ready(function(){
        var ctx = document.getElementById("canvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:['Costo total', 'Costos directos', 'Costos indirectos', 'Costo materiales'],
                  datasets: [{
                      data: ['{{round(($directcost+$indirectcost+$project->additionalMaterialCost+$project->materialCost), 2)}}', '{{round($directcost, 2)}}', '{{round(($indirectcost), 2)}}', '{{round($project->additionalMaterialCost+$project->materialCost, 2)}}'],
											backgroundColor: ['rgb(67, 142, 200, 0.5)', 'rgb(59, 198, 182, 0.5)', 'rgb(176, 124, 218, 0.5)', 'rgb(200, 200, 0, 0.5)'],
                      borderWidth: 1
                  }]
              },
              options: {
									legend: {
					          display: false
					        },
                  scales: {
                      yAxes: [{
                          ticks: {
                              beginAtZero:true
                          }
                      }]
                  }
              }
          });
	});
  </script>
@endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<a href="/project/{{$project->id}}">Volver al proyecto</a><br />
		<h1 class="d-inline">{{$project->name}}</h1>
		@if((Auth::user()->permissionCreateProject->state))
		<img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top" onclick="editProject({{$project->id}})" />
		@endif
		<h6>({{$project->projecttypes->name}})</h6>

		<div class="row mt-3">
		  <div class="col">
				<div class="panel panel-default">
           <div class="panel-heading mb-3"><b>Diagrama de costos</b></div>
           <div class="panel-body">
               <canvas id="canvas" height="280" width="600"></canvas>
           </div>
       </div>
			</div>
		  <div class="col mt-3">
				<ul>
					<li>{{$project->elementCount}} elementos contemplados</li>
					<li>{{$project->elementWithDirectCostCount}} elementos con costo directo asignado</li>
					<li class="mt-3">{{$project->materialCostCount}} elementos con costo de material calculado</li>
					<li>{{$project->additionalMaterialCostCount}} elementos con costo de material adicional asignado</li>
					<li class="mt-3">Peso total: {{round($project->weight, 2)}} Kg</li>
				</ul>
			</div>
		</div>
		<input id="tableSearchInput" type="text" placeholder="Buscar..." class="form-control mt-3">
		<br />
		<table id="projectelements" class="table table-hover">
			<thead class="thead-dark">
				<tr>
					<th class="d-2-none" title="Código único">Código</th>
					<th>Nombre</th>
					<th class="d-2-none">Subconjunto</th>
					<th>Cantidad</th>
					<th class="d-2-none">Estado</th>
					@if((Auth::user()->permissionViewOperationPrice->state))
						<th>Costo fabricacion directo</th>
					@endif
					@if((Auth::user()->permissionViewMaterialPrices->state))
						<th>Costo de material</th>
						<th>Costo de material adicional</th>
					@endif
					@if(Auth::user()->permissionViewElementsExt_f_1->state)
					<th class="d-2-none">{{strtoupper(config('constants.ext_f_1'))}}</th>
					@endif
				</tr>
			</thead>
			<tbody class="tbodyProjectelements">
				@foreach($project->projectelements as $projectelement)
					@if(($projectelement->specific_state_id == 1 AND Auth::user()->permissionViewElements->state) OR ($projectelement->specific_state_id == 3 AND Auth::user()->permissionViewHiddenElements->state))
				<tr class="background-color-row-state-{{$projectelement->specific_state_id}}">
					<td class="d-2-none" title="{{str_pad($projectelement->project_id, 3, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->subset->subset_number, 2, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->part, 2, '0', STR_PAD_LEFT)}}-{{$projectelement->subpart}}-{{$projectelement->version}}">{{$projectelement->element->nro}}-{{$projectelement->element->add}}</td>
					<td><a href="/projectelement/{{$projectelement->id}}">{{$projectelement->element->name}}</a></td>
					<td class="d-2-none">{{$projectelement->subset->name}}</td>
					<td>{{$projectelement->quantity}}</td>
					<td class="d-2-none">{{$projectelement->specific_state->name}}</td>
					@if((Auth::user()->permissionViewOperationPrice->state))
					<td>{{$projectelement->directCost}} USD</td>
					@endif
					@if((Auth::user()->permissionViewMaterialPrices->state))
					<td>{{round($projectelement->matCost, 2)}} USD
						@if(is_object($projectelement->matCostDate) AND ($projectelement->matCost>0))
							@if($projectelement->matCostDate < $outOfDate)
							<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Atención" data-content="El precio de este material es de {{$projectelement->matCostDate->diffForHumans()}} y pudo haber presentado cambios significativos. <a href='{{asset('/creatematerialprice/')}}' target='_blank' >Actualizar</a>."><img src="/images/warningIcon.png" alt="Atención" class="inline_icon pl-1 opacity-70"></a>
							@endif
						@endif
					</td>
					<td>{{$projectelement->addMatCost}} USD
						@if($projectelement->addMatCost>0)
							@if($projectelement->addMatCostDate < $outOfDate)
							<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Atención" data-content="El precio de este material es de {{$projectelement->addMatCostDate->diffForHumans()}} y pudo haber presentado cambios significativos. <a href='{{asset('/editelement/'.$projectelement->element->id)}}' target='_blank' >Actualizar</a>."><img src="/images/warningIcon.png" alt="Atención" class="inline_icon pl-1 opacity-70"></a>
							@endif
						@endif
					</td>
					@endif
					@if(Auth::user()->permissionViewElementsExt_f_1->state)
						@if(file_exists(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1')))
					<td class="text-center d-2-none"><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="{{strtoupper(config('constants.ext_f_1'))}} del elemento" title="{{strtoupper(config('constants.ext_f_1'))}} del elemento" onclick="goToExt_f_1('{{$projectelement->id}}','{{$projectelement->element->nro}}-{{$projectelement->element->add}} - {{$projectelement->element->name}}')" /></td>
						@else
					<td class="text-center d-2-none"><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" title="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" /></td>
						@endif
					@endif
				</tr>
					@endif
				@endforeach
			</tbody>
		</table>
	</div>
	<footer class="modal-footer">
		<div>Proyecto generado por {{$project->author->name}}</div>
	</footer>
@endsection
