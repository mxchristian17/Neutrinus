@extends('layouts.app')
@section('pageTitle'){{"Tipos de pedido -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Order_type.js')}}"></script>
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
		<h1 class="d-inline">Tipos de pedido</h1><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="El tipo de pedido de un elemento es la forma en que se hará el pedido del material (Barra, Planchuela, Hierro Ángulo, etc...). <a href='/help/order_types' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
		<br />
		@if((Auth::user()->permissionCreateOrder_type->state))
		<a href="/createorder_type">Crear nuevo tipo de pedido</a>
		@endif
		@if((Auth::user()->permissionViewHiddenOrder_types->state) OR (Auth::user()->permissionViewDisabledOrder_types->state) OR (Auth::user()->permissionViewDeletedOrder_types->state))
			@if($showAll)
		<a href="/ordertypes" class="float-right">Mostrar solo tipos de pedido habilitados</a>
			@else
		<a href="/ordertypes/1" class="float-right">Mostrar todos los tipos de pedido</a>
			@endif
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="order_types"class="table">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Fórmula</th>
				<th>Autor</th>
				<th>Estado</th>
			@if((Auth::user()->permissionCreateOrder_type->state))
				<th></th>
			@endif
			@if((Auth::user()->permissionDeleteOrder_type->state))
				<th></th>
			@endif
			</tr>
		</thead>
		<tbody id="tbodyOrder_types">
	@foreach ($order_types as $order_type)
		@if(!($order_type->state_id == 2 AND !Auth::user()->permissionViewDisabledOrder_types->state) AND !($order_type->state_id == 3 AND !Auth::user()->permissionViewHiddenOrder_types->state) AND !($order_type->state_id == 4 AND !Auth::user()->permissionViewDeletedOrder_types->state))
			<tr class="background-color-row-state-{{$order_type->state_id}}">
				<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="{{$order_type->description}}">{{$order_type->name}}</a></td>
				<td>{!!str_replace(array("$", "^2", "^3"), array("", "<sup>2</sup>", "<sup>3</sup>"), $order_type->original_formula)!!} <span class="text-secondary">[m<sup>3</sup>]</span></td>
				<td>By @can('seeUsersInformation')<a href="/user/{{$order_type->author->id}}">@endcan{{$order_type->author->name}}@can('seeUsersInformation')</a>@endcan</td>
				<td>{{$order_type->states->name}}</td>
			@if((Auth::user()->permissionCreateOrder_type->state))
				<td><img src="/images/editIcon.png" class="table_icon" alt="Editar" title="Editar" onclick="editOrder_type({{$order_type->id}})" /></td>
			@endif
			@if((Auth::user()->permissionDeleteOrder_type->state))
				@if($order_type->state_id!=4)
				<td id="{{$order_type->id}}"><img src="/images/trashIcon.png" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteOrder_type(this)" /></td>
				@else
				<td id="{{$order_type->id}}"><img src="/images/trashRedIcon.png" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteOrder_type(this)" /></td>
				@endif
			@endif
			</tr>
		@endif
	@endforeach
		</tbody>
	</table>
	{{$order_types->links()}}
@endsection
