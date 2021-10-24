@extends('layouts.app')
@section('pageTitle'){{$purchase_order->orderName.' -'}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Purchase.js')}}"></script>
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
		<h1 class="d-inline">{{$purchase_order->orderName}}</h1>
		@if((Auth::user()->permissionCreatePurchase_order->state))
		<img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top" onclick="editPurchase_order({{$purchase_order->id}})" />
		@endif
	<table class=" mt-3 table table-sm">
		<tr>
			<td>{{$purchase_order->orderType}} {{$purchase_order->orderName}}</td>
			<td>Estado: <span class="btn-sm {{$purchase_order->statusBtnClass}}">{{$purchase_order->statusName}}</span></td>
		</tr>
		@if((Auth::user()->permissionViewSuppliers->state))
		<tr>
			<td>Proveedor: <a href="{{asset('supplier/'.$purchase_order->supplier->id)}}" target="_blank">{{$purchase_order->supplier->name}}</a></td>
			<td>Mail: {{$purchase_order->supplier->email}}</td>
		</tr>
		<tr>
			<td>Dirección: {{$purchase_order->supplier->completeAddress}}</td>
			<td>Teléfono: {{$purchase_order->supplier->phone_number}}</td>
		</tr>
		@endif
		<tr>
			<td>@foreach($purchase_order->projects as $purchaseProject)
					@if($loop->first)
Proyectos: <a href="/project/{{$purchaseProject->project['id']}}" title="{{$purchaseProject->project->name}}" target="_blank">{{$purchaseProject->project->id}}</a>
					@else
- <a href="/project/{{$purchaseProject->project['id']}}" title="Proyecto para {{ Illuminate\Support\Str::lower($purchaseProject->project->projecttypes->name) }}" target="_blank">{{$purchaseProject->project->id}}</a>
					@endif

				 @endforeach
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Fecha de generación: {{\Carbon\Carbon::parse($purchase_order->created_at)->format('d/m/Y')}}</td>
			<td>Generada por: @can('seeUsersInformation')<a href="/user/{{$purchase_order->author->id}}" target="_blank">@endcan{{$purchase_order->author->name}}@can('seeUsersInformation')</a>@endcan</td>
		</tr>
		@if($purchase_order->status > 2 AND $purchase_order->status < 6)
		<tr>
			<td>Fecha de emisión: {{\Carbon\Carbon::parse($purchase_order->emitted_date)->format('d/m/Y')}}</td>
			<td>Emitida por: @can('seeUsersInformation')<a href="/user/{{$purchase_order->emitter->id}}" target="_blank">@endcan{{$purchase_order->emitter->name}}@can('seeUsersInformation')</a>@endcan</td>
		</tr>
		@endif
		@if($purchase_order->status > 2 AND $purchase_order->status < 6)
		<tr>
			<td>Fecha solicitada: {{\Carbon\Carbon::parse($purchase_order->requested_delivery_date)->format('d/m/Y')}}</td>
			<td>@if($purchase_order->status > 3 AND $purchase_order->status < 6) Recibida por: @can('seeUsersInformation')<a href="/user/{{$purchase_order->recipient->id}}" target="_blank">@endcan{{$purchase_order->recipient->name}}@can('seeUsersInformation')</a>@endcan <small> ({{\Carbon\Carbon::parse($purchase_order->effective_delivery_date)->format('d/m/Y')}})</small>@endif</td>
		</tr>
		@endif
		@if((Auth::user()->permissionViewPurchase_OrderPrices->state))
		<tr>
			<td><b>Valor cotizado:</b> @if(($purchase_order->quotedValue>0) AND ($purchase_order->status!=0)){{$purchase_order->quotedValue}} USD @else No cotizado @endif </td>
			<td><b>Valor estimado:</b> {{$purchase_order->estimatedValue}} USD</td>
		</tr>
		@endif
	</table>
	<input class="form-control d-print-none" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="purchase_orders" class="table table-hover">
		<thead class="thead-dark">
			<tr>
				<th>Código</th>
				<th>Nombre</th>
				<th>Material</th>
				<th>Cantidad</th>
				@if((Auth::user()->permissionCreatePurchase_order->state) AND ($purchase_order->status<3))
				<th></th>
				@endif
				@if((Auth::user()->permissionDeletePurchase_order->state) AND ($purchase_order->status<3))
				<th></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodyPurchase_orders">
	@foreach ($purchase_order->elements as $purchase_element)
		<tr>
			<td><a href="/element/{{$purchase_element->element_id}}" title="Ver detalle de {{$purchase_element->element->name}}" target="_blank">{{$purchase_element->element->nro}}-{{$purchase_element->element->add}}</a></td>
			<td>{{$purchase_element->element->name}}</td>
			<td>{{$purchase_element->element->order_type->name}} {{$purchase_element->element->material->initials}} {{$purchase_element->element->dimensions}}</td>
			<td>{{$purchase_element->quantity}}</td>
			@if((Auth::user()->permissionCreatePurchase_order->state) AND ($purchase_order->status<3))
				<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editPurchase_order({{$purchase_order->id}})" /></td>
			@endif
			@if((Auth::user()->permissionDeletePurchase_order->state) AND ($purchase_order->status<3))
				@if($purchase_order->state_id!=4)
			<td id="{{$purchase_order->id}}"><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="deletePurchase_order(this,'none')" /></td>
				@else
			<td id="{{$purchase_order->id}}"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeletePurchase_order(this,'none')" /></td>
				@endif
			@endif
		</tr>
	@endforeach
		</tbody>
	</table>
@endsection
