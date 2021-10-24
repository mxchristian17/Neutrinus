@extends('layouts.app')
@section('pageTitle'){{$element->name}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Element.js')}}"></script>
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
		<h1  class="d-inline">{{$element->name}}</h1>
		<ul class="navbar-nav mr-auto float-right d-inline">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
						Opciones <span class="caret"></span>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
					@if((Auth::user()->permissionCreateElement->state))
						<a class="dropdown-item" href="#" onclick="editElement({{$element->id}})" />Editar elemento</a>
					@endif
					@if(Auth::user()->permissionDeleteElement->state)
						@if($element->general_state_id!=4)
					<a class="dropdown-item" href="#" onclick="deleteElement({{$element->id}},'{{$element->name}}')">Eliminar elemento</a>
						@else
					<a class="dropdown-item" href="#" onclick="definitiveDeleteElement({{$element->id}},'{{$element->name}}', '{{$element->project_id}}')">Eliminar elemento definitivamente</a>
						@endif
					@endif
				</div>
			</li>
		</ul>
		<br />
		<table id="projects"class="table">
			<thead>
				<tr>
					<th>Propiedad</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Código general del elemento</td>
					<td>{{$element->nro}}-{{$element->add}}</td>
				</tr>
				@if(Auth::user()->permissionViewElementsExt_f_1->state)
				<tr>
					<td>{{strtoupper(config('constants.ext_f_1'))}}</td>
					@if(file_exists(storage_path('app').'/files/ext_f_1/elements/'.$element->nro.'-'.$element->add.'.'.config('constants.ext_f_1')))
					<td><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="{{strtoupper(config('constants.ext_f_1'))}} del elemento" title="{{strtoupper(config('constants.ext_f_1'))}} del elemento" onclick="goToExt_f_1('{{$element->id}}','{{$element->nro}}-{{$element->add}} - {{$element->name}}')" /></td>
					@else
					<td><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" title="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" /></td>
					@endif
				</tr>
				@endif
				@if(Auth::user()->permissionViewElementFolder->state)
				<tr class="d-2-none">
					<td>Carpeta</td>
					<td class="d-2-none"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(2,{{$element->id}})" /></td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewOwedItems->state)
				<tr>
					<td>Cantidad pendiente de entregar</td>
					<td>{{$todoQty}}</td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewMaterials->state)
				<tr>
					<td>Material</td>
					<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="{{$element->material->description}}">{{$element->material->name}}</a></td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewOrder_types->state)
				<tr>
					<td>Tipo de pedido</td>
					<td>{{$element->order_type->name}}</td>
				</tr>
				<tr>
					<td>Material fragmentable<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los materiales fragmentables son aquellos que salen de un material del cual se obtendrán varias piezas. Por ejemplo, si la pieza se fabricará en un torno de control numérico, se utilizará una barra de la cual salen muchas piezas. En este caso hablamos de un material fragmentable. Por el contrario hay casos en que cada pieza se fabrica de forma individual a base de un material que se pidió especificamente para esa pieza. Por ejemplo en el caso de que se vaya a fabricar una pieza de diámetro mayor al del pasaje de barra en un torno convencional. Siempre se considerará a la fragmentación respecto del largo del material."><img src="/images/helpIcon.png" alt="Ayuda" title="¿Qué es esto?" class="inline_icon pl-1 opacity-70 align-top"></a></td>
					<td>@if($element->shared_material)Si @else No @endif</td>
				</tr>
				@if($element->order_type->d_ext)
				<tr>
					<td>Diámetro exterior</td>
					<td>{{$element->d_ext}} mm</td>
				</tr>
					@endif
					@if($element->order_type->d_int)
				<tr>
					<td>Diámetro interior</td>
					<td>{{$element->d_int}} mm</td>
				</tr>
					@endif
					@if($element->order_type->side_a)
				<tr>
					<td>Lado A</td>
					<td>{{$element->side_a}} mm</td>
				</tr>
					@endif
					@if($element->order_type->side_b)
				<tr>
					<td>Lado B</td>
					<td>{{$element->side_b}} mm</td>
				</tr>
					@endif
					@if($element->order_type->large)
				<tr>
					<td>Largo</td>
					<td>{{$element->large}} mm</td>
				</tr>
					@endif
					@if($element->order_type->width)
				<tr>
					<td>Ancho</td>
					<td>{{$element->width}} mm</td>
				</tr>
					@endif
					@if($element->order_type->thickness)
				<tr>
					<td>Espesor</td>
					<td>{{$element->thickness}} mm</td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewOrder_types->state AND Auth::user()->permissionViewMaterials->state)
					@if($element->volume!=0)
				<tr>
					<td>Volúmen</td>
					<td>{{round($element->volume, 2)}} <span class="text-secondary">[cm<sup>3</sup>]</span></td>
				</tr>
					@endif
					@if($element->weight!=0)
				<tr>
					<td>Peso</td>
					<td>{{round($element->weight, 3)}} [Kg]</td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewOperationPrice->state)
					@if($element->directCost>0)
				<tr>
					<td>Costo directo de producción</td>
					<td>{{round($element->directCost, 3)}} USD</td>
				</tr>
					@endif
				@endif
				@if(Auth::user()->permissionViewMaterialPrices->state)
				<tr>
					<td>Costo de material calculado</td>
					<td>{{round($element->materialCost[0], 2)}} USD</td>
				</tr>
					@if($element->additional_material_cost>0)
				<tr>
					<td>Costo de material adicional</td>
					<td>{{round($element->additional_material_cost, 3)}} USD</td>
				</tr>
					@endif
				@endif
				<tr>
					<td>Cantidad mínima a fabricar por serie</td>
					<td>{{$element->quantity_per_manufacturing_series}}</td>
				</tr>
				<tr>
					<td>Estado de pieza general</td>
					<td><span  class="btn-sm {{classForGeneralStateTitle($element->general_state_id)}}">{{$element->general_state->name}}</span></td>
				</tr>
				<tr>
					<td>Descripción/Observaciones</td>
					<td>{{$element->description}}</td>
				</tr>
				<tr>
					<td>Códigos de proveedores</td>
					<td>
						@foreach($element->supplier_code as $supplier_code)
						@if((Auth::user()->permissionViewSuppliers->state & ($supplier_code->state_id == 1))) <a href="{{asset('supplier/'.$supplier_code->supplier->id)}}" target="_blank" class="text-secondary">{{$supplier_code->supplier->name}}</a>: <a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="click" data-content="{{$supplier_code->code}}: {{$supplier_code->description}}<br /> <span class='text-secondary'>Cargado por {{$supplier_code->author->name}} el {{\Carbon\Carbon::parse($supplier_code->created_at)->format('d/m/y')}}</span><br /> @if((Auth::user()->permissionEditSupplier_code->state))<a href='{{asset('deletesuppliercode')}}/{{$supplier_code->id}}/{{ csrf_token() }}'>Eliminar código</a> @endif">{{$supplier_code->code}}</a> <br /> @endif @endforeach
						@if((Auth::user()->permissionEditSupplier_code->state))<button type="button" class="btn btn-link p-0 m-0" data-toggle="modal" data-target="#addSupplierCodeModal"><img src="{{asset('/images/addIcon.png')}}" class="inline_icon" alt="Añadir" title="Añadir" /></button>
						<!-- Modal -->
						<div class="modal fade" id="addSupplierCodeModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierCodeModalTitle" aria-hidden="true">
						  <div class="modal-dialog modal-dialog-centered" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLongTitle">Añadir código según proveedor</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
										{!! Form::open(['url' => '/storesuppliercode', 'class' => 'container-sm']) !!}
						          <div class="form-group">
						            <label for="recipient-name" class="col-form-label">Proveedor:</label>
												{!! Form::select('supplier_id', $supplier_id, old('supplier_id'),['class'=>'form-control']) !!}
						          </div>
						          <div class="form-group">
												<label>Código:</label>
												{!! Form::text('code', old('code') ?? '', array_merge(['class' => 'form-control'], ['autocomplete' => 'off'])) !!}
						          </div>
											{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control'], ['rows' => '2'])) !!}
											{{ Form::hidden('element_id', $element->id) }}
											{!! Form::submit('Añadir código', array_merge(['class' => 'btn btn-primary mt-2 mb-2'])) !!}
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
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						      </div>
						    </div>
						  </div>
						</div>
						@endif
					</td>
				</tr>
				<tr>
					<td>Autor de la pieza</td>
					<td>{{$element->author->name}}</td>
				</tr>
			</tbody>
		</table>
		<hr/>
		@if((Auth::user()->permissionViewOperations->state) AND ((Auth::user()->permissionViewHiddenOperations->state) OR (Auth::user()->permissionViewDisabledOperations->state) OR (Auth::user()->permissionViewDeletedOperations->state)))
			@if($showAllOperations)
		<a href="/element/{{$element->id}}" class="float-right">Mostrar solo las rutas habilitadas</a>
			@else
		<a href="/element/{{$element->id}}/1" class="float-right">Mostrar todas las rutas</a>
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
				@if((Auth::user()->permissionDeleteOperation->state))
				<th></th>
				@endif
			</thead>
			<tbody>
				@foreach($element->operation as $operation)
					@if($operation->operation_state_id == 1 OR ($showAllOperations AND $operation->operation_state_id == 2 AND Auth::user()->permissionViewDisabledOperations->state) OR ($showAllOperations AND $operation->operation_state_id == 3 AND Auth::user()->permissionViewHiddenOperations->state) OR ($showAllOperations AND $operation->operation_state_id == 4 AND Auth::user()->permissionViewDeletedOperations->state))
				<tr class="background-color-row-state-{{$operation->operation_state_id}}">
					<td>{{$operation->operation_name->name}}</td>
					<td class="d-2-none">{{$operation->observation}}</td>
					<td>{{$operation->preparation_time}} min</td>
					<td>{{$operation->manufacturing_time}} min</td>
					@if(Auth::user()->permissionViewOperationPrice->state)
					<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="El valor calculado es para 1 elemento pero se considera que se fabrican {{$element->quantity_per_manufacturing_series}} elementos por serie. El valor por hora configurado es de {{$operation->operation_name->usd_for_hour}} USD.">{{$operation->cost}}<span class="d-3-none">  USD</span></a></td>
					@endif
					<td title="{{$operation->cnc_program}}" class="max-col-char">{{$operation->cnc_program}}</td>
					@if(Auth::user()->permissionViewOperationFolder->state)
					<td class="d-2-none col-xs-1 text-center"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(3,{{$operation->id}})" /></td>
					@endif
					<td class="max-col-char">{{$operation->states->name}}</td>
					@if((Auth::user()->permissionCreateOperation->state))
						<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editOperation({{$operation->id}})" /></td>
					@endif
					@if((Auth::user()->permissionDeleteOperation->state))
					@if($operation->operation_state_id!=4)
						<td><a href='{{asset('deleteoperation')}}/{{$operation->id}}/{{ csrf_token() }}'><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" /></a></td>
					@else
						<td><a href='{{asset('definitivedeleteoperation')}}/{{$operation->id}}/{{ csrf_token() }}'><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" /></a></td>
					@endif
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
			{{ Form::hidden('element_id', $element->id) }}
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
		<div>Elemento generado por {{$element->author->name}}</div>
	</footer>
@endsection
