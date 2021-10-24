@extends('layouts.app')
@section('pageTitle')Elemento {{str_pad($projectelement->project_id, 3, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->subset_id, 2, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->part, 2, '0', STR_PAD_LEFT)}}-{{$projectelement->subpart}}-{{$projectelement->version}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Projectelement.js')}}"></script>
	<script type="application/javascript" src="{{ asset('js/Operation.js')}}"></script>
	<script src="{{ asset('js/Chart.bundle.js')}}" charset="utf-8"></script>
@if(Auth::user()->permissionViewOwedItems->state and count($todo))
	<script>
	var timeFormat = 'DD/MM/YYYY';
	var color = Chart.helpers.color;

var config = {
		type:    'bar',
		data:    {
				labels: [
					@foreach($todo as $todoElement)
					@if($loop->first)new Date({{\Carbon\Carbon::now()->year}},{{\Carbon\Carbon::now()->month-1}},{{\Carbon\Carbon::now()->day}}), @endif
					new Date({{\Carbon\Carbon::parse($todoElement[0])->year}},{{\Carbon\Carbon::parse($todoElement[0])->month-1}},{{\Carbon\Carbon::parse($todoElement[0])->day}}),
					@if($loop->last)
					new Date({{\Carbon\Carbon::parse($todoElement[0])->year}},{{\Carbon\Carbon::parse($todoElement[0])->month-1}},{{\Carbon\Carbon::parse($todoElement[0])->addDays(1)->day}}),
					@endif
					@endforeach
				],
				datasets: [
						{
								type: 'bar',
								label: "",
								data: [
									@foreach($todo as $todoElement)
									@if($loop->first) 0, @endif
									{{$todoElement[1]}},
									@if($loop->last) 0 @endif
									@endforeach
								],
								borderColor: 'rgba(255, 0, 0, 0.5)',
								borderWidth: 1,
								backgroundColor: 'rgba(255, 0, 0, 0.2)',
		}]},
		options: {
						responsive: true,
						title:      {
								display: true,
								text:    "Entregas programadas pendientes"
						},
						scales:     {
								xAxes: [{
										type:       "time",
										time:       {
												unit: 'day',
												tooltipFormat: 'DD/MM/YYYY',
										},
										scaleLabel: {
												display:     true,
										}
								}],
								yAxes: [{
										scaleLabel: {
												display:     true,
												labelString: 'Cantidad'
										}
								}]
						}
				}
};
	$(document).ready(function(){
				var ctx = document.getElementById("canvas").getContext('2d');
				window.myLine = new Chart(ctx, config);
	});
	</script>
@endif
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
		<a href="/project/{{$projectelement->project_id}}">Volver al proyecto</a><br />
		<h1 class="d-inline">{{$projectelement->element->name}}</h1>
		<ul class="navbar-nav mr-auto float-right d-inline">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
						Opciones <span class="caret"></span>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
					@if((Auth::user()->permissionCreateProjectelement->state))
						<a class="dropdown-item" href="#" onclick="editProjectelement({{$projectelement->id}})" />Editar elemento</a>
					@endif
					@if(Auth::user()->permissionDeleteProjectelement->state)
						@if($projectelement->specific_state_id!=4)
					<a class="dropdown-item" href="#" onclick="deleteProjectelement({{$projectelement->id}},'{{$projectelement->element->name}}')">Eliminar elemento</a>
						@else
					<a class="dropdown-item" href="#" onclick="definitiveDeleteProjectelement({{$projectelement->id}},'{{$projectelement->element->name}}', '{{$projectelement->project_id}}')">Eliminar elemento definitivamente</a>
						@endif
					@endif
				</div>
			</li>
		</ul>
		<br />
		<table id="projects" class="table first_col_limited">
			<thead>
				<tr>
					<th>Propiedad</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Proyecto</td>
					<td>{{$projectelement->project->name}}</td>
				</tr>
				<tr>
					<td>Subconjunto</td>
					<td>{{$projectelement->subset->name}}</td>
				</tr>
				@if(Auth::user()->permissionViewElementsExt_f_1->state)
				<tr>
					<td>{{strtoupper(config('constants.ext_f_1'))}}</td>
					@if(file_exists(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1')))
					<td><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="{{strtoupper(config('constants.ext_f_1'))}} del elemento" title="{{strtoupper(config('constants.ext_f_1'))}} del elemento" onclick="goToExt_f_1('{{$projectelement->id}}','{{$projectelement->element->nro}}-{{$projectelement->element->add}} - {{$projectelement->element->name}}')" /></td>
					@else
					<td><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" title="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" /></td>
					@endif
				</tr>
				@endif
				@if(Auth::user()->permissionViewElementFolder->state)
				<tr class="d-2-none">
					<td>Carpeta</td>
					<td class="d-2-none"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(2,{{$projectelement->element->id}})" /></td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewMaterials->state)
				<tr>
					<td>Material</td>
					<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="{{$projectelement->element->material->description}}">{{$projectelement->element->material->name}}</a></td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewOrder_types->state)
				<tr>
					<td>Tipo de pedido</td>
					<td>{{$projectelement->element->order_type->name}}</td>
				</tr>
					@if($projectelement->element->order_type->d_ext)
				<tr>
					<td>Diámetro exterior</td>
					<td>{{$projectelement->element->d_ext}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->d_int)
				<tr>
					<td>Diámetro interior</td>
					<td>{{$projectelement->element->d_int}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->side_a)
				<tr>
					<td>Lado A</td>
					<td>{{$projectelement->element->side_a}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->side_b)
				<tr>
					<td>Lado B</td>
					<td>{{$projectelement->element->side_b}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->large)
				<tr>
					<td>Largo</td>
					<td>{{$projectelement->element->large}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->width)
				<tr>
					<td>Ancho</td>
					<td>{{$projectelement->element->width}} mm</td>
				</tr>
					@endif
					@if($projectelement->element->order_type->thickness)
				<tr>
					<td>Espesor</td>
					<td>{{$projectelement->element->thickness}} mm</td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewOrder_types->state AND Auth::user()->permissionViewMaterials->state)
					@if($projectelement->element->volume!=0)
				<tr>
					<td>Volúmen</td>
					<td>{{round($projectelement->element->volume, 2)}} <span class="text-secondary">[cm<sup>3</sup>]</span></td>
				</tr>
					@endif
					@if($projectelement->element->weight!=0)
				<tr>
					<td>Peso</td>
					<td>{{round($projectelement->element->weight, 3)}} <span class="text-secondary">[Kg]</span></td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewOperationPrice->state)
					@if($projectelement->directCost>0)
				<tr>
					<td>Costo directo de producción <small class="text-secondary">(x{{$projectelement->quantity}})<small></td>
					<td>{{round($projectelement->directCost, 3)}} USD</td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewMaterialPrices->state)
				<tr>
					<td>Costo de material calculado <small class="text-secondary">(x{{$projectelement->quantity}})<small></td>
					<td>{{round($projectelement->element->materialCost[0]*$projectelement->quantity, 2)}} USD</td>
				</tr>
					@if($projectelement->element->additional_material_cost>0)
				<tr>
					<td>Costo de material adicional  <small class="text-secondary">(x{{$projectelement->quantity}})</small></td>
					<td>{{round($projectelement->element->additional_material_cost*$projectelement->quantity, 2)}} USD</td>
				</tr>
					@endif
				@endif
				<tr>
					<td>Cantidad en el proyecto</td>
					<td>{{$projectelement->quantity}} <small>(Se fabrican {{$projectelement->element->quantity_per_manufacturing_series}} por serie)</small></td>
				</tr>
				<tr>
					<td>Estado de pieza general</td>
					<td><span  class="btn-sm {{classForGeneralStateTitle($projectelement->element->general_state_id)}}">{{$projectelement->element->general_state->name ?? 'Este elemento general se ha eliminado de forma definitiva.'}}</span></td>
				</tr>
				<tr>
					<td>Estado de pieza especifico</td>
					<td><span  class="btn-sm {{classForGeneralStateTitle($projectelement->specific_state_id)}}">{{$projectelement->specific_state->name}}</span></td>
				</tr>
				<tr>
					<td>Código general del elemento</td>
					<td>
						@if($projectelement->element->general_state_id == 1 OR ($projectelement->element->general_state_id == 2 AND Auth::user()->permissionViewDisabledElements->state) OR ($projectelement->element->general_state_id == 3 AND Auth::user()->permissionViewHiddenElements->state) OR ($projectelement->element->general_state_id == 4 AND Auth::user()->permissionViewDeletedElements->state))
						<a href="/element/{{$projectelement->element->id}}" title="Ver detalles de elemento general" target="_blank" >
						@endif
							{{$projectelement->element->nro}}-{{$projectelement->element->add}}
						@if($projectelement->element->general_state_id == 1 OR ($projectelement->element->general_state_id == 2 AND Auth::user()->permissionViewDisabledElements->state) OR ($projectelement->element->general_state_id == 3 AND Auth::user()->permissionViewHiddenElements->state) OR ($projectelement->element->general_state_id == 4 AND Auth::user()->permissionViewDeletedElements->state))
						</a>
						@endif
					</td>
				</tr>
				<tr>
					<td>Código del elemento en el proyecto</td>
					<td>{{$projectelement->projectCode}}</td>
				</tr>
				<tr>
					<td>Descripción/Observaciones</td>
					<td>{{$projectelement->element->description}}</td>
				</tr>
				<tr>
					<td>Elemento incluido al proyecto por</td>
					<td>{{$projectelement->author->name}}</td>
				</tr>
			</tbody>
		</table>
		<hr/>
		@if((Auth::user()->permissionViewOperations->state) AND ((Auth::user()->permissionViewHiddenOperations->state) OR (Auth::user()->permissionViewDisabledOperations->state) OR (Auth::user()->permissionViewDeletedOperations->state)))
			@if($showAllOperations)
		<a href="/projectelement/{{$projectelement->id}}" class="float-right">Mostrar solo las rutas habilitadas</a>
			@else
		<a href="/projectelement/{{$projectelement->id}}/1" class="float-right">Mostrar todas las rutas</a>
			@endif
		@endif
		@if(Auth::user()->permissionViewOperations->state)
		<h3>Proceso de fabricación</h3>
		<table id="rutas" class="table table-hover">
			<thead class="thead-dark">
				<th>Ruta</th>
				<th class="d-2-none">Observaciones</th>
				<th title="Tiempo de preparación" class="max-col-char">Preparación</th>
				<th title="Tiempo de fabricación" class="max-col-char">Fabricación</th>
				@if(Auth::user()->permissionViewOperationPrice->state)
				<th title="Costo en USD de fabricación">Costo<span class="d-3-none"> [USD]</span></th>
				@endif
				<th>Programa</th>
				@if(Auth::user()->permissionViewOperationFolder->state)
				<th class="d-2-none col-xs-1 text-center">Carpeta</th>
				@endif
				<th>Estado</th>
				@if((Auth::user()->permissionCreateOperation->state))
				<th></th>
				@endif
			</thead>
			<tbody>
				@foreach($projectelement->element->operation as $operation)
					@if($operation->operation_state_id == 1 OR ($showAllOperations AND $operation->operation_state_id == 2 AND Auth::user()->permissionViewDisabledOperations->state) OR ($showAllOperations AND $operation->operation_state_id == 3 AND Auth::user()->permissionViewHiddenOperations->state) OR ($showAllOperations AND $operation->operation_state_id == 4 AND Auth::user()->permissionViewDeletedOperations->state))
				<tr class="background-color-row-state-{{$operation->operation_state_id}}">
					<td>{{$operation->operation_name->name}}</td>
					<td class="d-2-none">{{$operation->observation}}</td>
					<td>{{$operation->preparation_time}} min</td>
					<td>{{$operation->manufacturing_time}} min</td>
					@if(Auth::user()->permissionViewOperationPrice->state)
					<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="El valor calculado es para {{$projectelement->quantity}} @if($projectelement->quantity>1)elementos @else elemento @endif pero se considera que se fabrican {{$projectelement->element->quantity_per_manufacturing_series}} elementos por serie. El valor por hora configurado es de {{$operation->operation_name->usd_for_hour}} USD.">{{$operation->cost}}<span class="d-3-none">  USD</span></a></td>
					@endif
					<td title="{{$operation->cnc_program}}" class="max-col-char">{{$operation->cnc_program}}</td>
					@if(Auth::user()->permissionViewOperationFolder->state)
					<td class="d-2-none col-xs-1 text-center"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(3,{{$operation->id}})" /></td>
					@endif
					<td class="max-col-char">{{$operation->states->name}}</td>
					@if((Auth::user()->permissionCreateOperation->state))
						<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editOperation({{$operation->id}})" /></td>
					@endif
				</tr>
					@endif
				@endforeach
		</tbody>
		</table>
		<hr/>
			@if((Auth::user()->permissionCreateOperation->state) AND (Auth::user()->permissionViewOperations->state))
			<h3 class="container-sm d-inline">Crear ruta</h3><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Las rutas son los posibles caminos que recorrerá un elemento hasta estar dispuesto para la venta. En el caso de ser un elemento que se fabrica en la planta, un tipo de ruta puede ser corte a serrucho sin fin, otro puede ser Torno, otro pintura, etc... <a href='/help/operations' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
			{!! Form::open(['url' => '/storeoperation', 'class' => 'container-sm']) !!}
			<div class="form-row">
				<div class="col">
					{!! Form::Label('operation_name_id', 'Ruta:', ['class' => 'control-label mt-2']) !!}
					{!! Form::select('operation_name_id', $operation_names, null, ['class' => 'form-control']) !!}
				</div>
    		<div class="col">
					{!! Form::Label('order', 'Insertar luego de:', ['class' => 'control-label mt-2']) !!}
					{!! Form::select('order', $operations_order, null, ['class' => 'form-control']) !!}
				</div>
			</div>
			<div class="form-row">
				<div class="col">
					{!! Form::Label('preparation_time', 'Tiempo de preparación [min]:', ['class' => 'control-label mt-2']) !!}
					{!! Form::number('preparation_time', old('preparation_time') ?? 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '0'])) !!}
				</div>
    		<div class="col">
					{!! Form::Label('manufacturing_time', 'Tiempo de fabricación [min]:', ['class' => 'control-label mt-2']) !!}
					{!! Form::number('manufacturing_time', old('manufacturing_time') ?? 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '0'])) !!}
				</div>
			</div>
			<div class="form-row">
				<div class="col">
					{!! Form::Label('cnc_program', 'Programa:', ['class' => 'control-label mt-2']) !!}
					{!! Form::text('cnc_program', old('cnc_program') ?? '', array_merge(['class' => 'form-control'], ['autocomplete' => 'off'])) !!}
				</div>
    		<div class="col">
					{!! Form::Label('operation_state_id', 'Estado:', ['class' => 'control-label mt-2']) !!}
					{!! Form::select('operation_state_id', $operation_states, null, ['class' => 'form-control']) !!}
				</div>
			</div>
			{!! Form::Label('observation', 'Observaciones de ruta:', ['class' => 'control-label']) !!}
			{!! Form::textarea('observation', old('observation'), array_merge(['class' => 'form-control'], ['rows' => '2'])) !!}
			{{ Form::hidden('element_id', $projectelement->element->id) }}
			{!! Form::submit('Añadir ruta', array_merge(['class' => 'btn btn-primary mt-2 mb-2'], ['onclick' => 'checkSubmit(event)'])) !!}
			@if ($errors->any())
					<div class="alert alert-danger mt-2">
							<ul>
									@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
									@endforeach
							</ul>
					</div>
			@endif
			{!! Form::close() !!}
			@endif
		@endif
		@if(Auth::user()->permissionViewOwedItems->state and count($todo))
		<div class="panel panel-default">
			 <div class="panel-heading mb-3"><b>Diagrama de entregas pendientes</b></div>
			 <div class="panel-body">
					 <canvas id="canvas" height="280" width="600"></canvas>
			 </div>
		</div>
		@endif
	</div>
	<footer class="modal-footer">
		<div>Elemento generado por {{$projectelement->author->name}}</div>
	</footer>
@endsection
