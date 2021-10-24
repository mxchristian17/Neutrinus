@extends('layouts.app')
@section('pageTitle'){{"Rutas -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Operation.js')}}"></script>
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
		<h1 class="d-inline">Rutas</h1><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Las rutas son los posibles caminos que recorrerá un elemento hasta estar dispuesto para la venta. En el caso de ser un elemento que se fabrica en la planta, un tipo de ruta puede ser corte a serrucho sin fin, otro puede ser Torno, otro pintura, etc... <a href='/help/operations' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
		<br />
		@if((Auth::user()->permissionCreateOperation->state))
		<a href="/createoperation_name">Crear nuevo tipo de ruta</a>
		@endif
		@if((Auth::user()->permissionViewHiddenOperations->state) OR (Auth::user()->permissionViewDisabledOperations->state) OR (Auth::user()->permissionViewDeletedOperations->state))
			@if($showAll)
		<a href="/operation_names" class="float-right">Mostrar solo rutas habilitadas</a>
			@else
		<a href="/operation_names/1" class="float-right">Mostrar todas las rutas</a>
			@endif
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="operation_names"class="table">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Estado</th>
			@if((Auth::user()->permissionViewOperationPrice->state))
				<th title="Costo en USD por hora">USD/Hr</th>
			@endif
			@if((Auth::user()->permissionCreateOperation->state))
				<th></th>
			@endif
			@if((Auth::user()->permissionDeleteOperation->state))
				<th></th>
			@endif
			</tr>
		</thead>
		<tbody id="tbodyOperation_names">
	@foreach ($operation_names as $operation_name)
		@if(!($operation_name->state_id == 2 AND !Auth::user()->permissionViewDisabledOperations->state) AND !($operation_name->state_id == 3 AND !Auth::user()->permissionViewHiddenOperations->state) AND !($operation_name->state_id == 4 AND !Auth::user()->permissionViewDeletedOperations->state))
			<tr class="background-color-row-state-{{$operation_name->state_id}}">
				<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="{{$operation_name->description}}">{{$operation_name->name}}</a></td>
				<td>{{$operation_name->states->name}}</td>
			@if((Auth::user()->permissionViewOperationPrice->state))
				<td>{{round($operation_name->usd_for_hour,2)}} USD</td>
			@endif
			@if((Auth::user()->permissionCreateOperation->state))
				<td><img src="/images/editIcon.png" class="table_icon" alt="Editar" title="Editar" onclick="editOperation_name({{$operation_name->id}})" /></td>
			@endif
			@if((Auth::user()->permissionDeleteOperation->state))
				@if($operation_name->state_id!=4)
				<td id="{{$operation_name->id}}"><img src="/images/trashIcon.png" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteOperation_name(this)" /></td>
				@else
				<td id="{{$operation_name->id}}"><img src="/images/trashRedIcon.png" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteOperation_name(this)" /></td>
				@endif
			@endif
			</tr>
		@endif
	@endforeach
		</tbody>
	</table>
@endsection
