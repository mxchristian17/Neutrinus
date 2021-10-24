@extends('layouts.app')
@section('pageTitle'){{$supplier->name}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{asset('js/Supplier.js')}}"></script>
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
		<a href="/suppliers" class="d-block">Volver a proveedores</a>
		<h1 class="d-inline">{{$supplier->name}}</h1>
		@if((Auth::user()->permissionCreateSupplier->state))
		<img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top" onclick="editSupplier({{$supplier->id}})" />
		@endif
		<table id="projects"class="table">
			<thead>
				<tr>
					<th colspan="2">Información del proveedor</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Nombre</td>
					<td>{{$supplier->name}}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>{{$supplier->email}}</td>
				</tr>
				<tr>
					<td>Teléfono</td>
					<td>{{$supplier->phone_number}}</td>
				</tr>
				<tr>
					<td>Dirección</td>
					<td>{{$supplier->completeAddress}}</td>
				</tr>
				<tr>
					<td>Descripción del proveedor</td>
					<td>{{$supplier->description}}</td>
				</tr>
				<tr>
					<td>Estado de proveedor</td>
					<td><span  class="btn-sm {{classForGeneralStateTitle($supplier->state_id)}}">{{$supplier->states->name}}</span></td>
				</tr>
				@if(Auth::user()->permissionViewPurchase_OrderPrices->state)
				<tr>
					<td>Moneda de facturación</td>
					<td>{{$supplier->currency->name}}</td>
				</tr>
				<tr>
					<td>Tipo de contribuyente</td>
					<td>{{$supplier->taxPayerName}}</td>
				</tr>
				<tr>
					<td>CUIL / CUIT</td>
					<td>{{$supplier->cuit}}</td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewPurchase_Orders->state)
				<tr>
					<td colspan="2"><a class="btn btn-primary btn-block" href="{{asset('purchase_orders/'.$supplier->id)}}">Ver compras historicas</a></td>
				</tr>
				@endif
			</tbody>
		</table>
		<table class="table">
			<thead>
				<th>Contacto</th>
				<th>Teléfono</th>
				<th>Email</th>
				<th>Estado</th>
				<th>Descripción</th>
			</thead>
			<tbody>
				@foreach($supplier->contacts as $contact)
				<tr>
					<td>{{$contact->name}}</td>
					<td>{{$contact->phone_number}}</td>
					<td>{{$contact->email}}</td>
					<td>{{$contact->states->name}}</td>
					<td>{{$contact->description}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
@endsection
