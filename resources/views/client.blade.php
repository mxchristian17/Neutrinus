@extends('layouts.app')
@section('pageTitle'){{$client->name}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{asset('js/Client.js')}}"></script>
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
		<a href="/clients" class="d-block">Volver a clientes</a>
		<h1 class="d-inline">{{$client->name}}</h1>
		@if((Auth::user()->permissionCreateClient->state))
		<img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top" onclick="editClient({{$client->id}})" />
		@endif
		<table id="projects"class="table">
			<thead>
				<tr>
					<th colspan="2">Información del cliente</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Nombre</td>
					<td>{{$client->name}}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>{{$client->email}}</td>
				</tr>
				<tr>
					<td>Teléfono</td>
					<td>{{$client->phone_number}}</td>
				</tr>
				<tr>
					<td>Dirección</td>
					<td>{{$client->completeAddress}}</td>
				</tr>
				<tr>
					<td>Descripción del cliente</td>
					<td>{{$client->description}}</td>
				</tr>
				<tr>
					<td>Estado de cliente</td>
					<td><span  class="btn-sm {{classForGeneralStateTitle($client->state_id)}}">{{$client->states->name}}</span></td>
				</tr>
				@if(Auth::user()->permissionViewPurchase_OrderPrices->state)
				<tr>
					<td>Moneda de facturación</td>
					<td>{{$client->currency->name}}</td>
				</tr>
				<tr>
					<td>Tipo de contribuyente</td>
					<td>{{$client->taxPayerName}}</td>
				</tr>
				<tr>
					<td>CUIL / CUIT</td>
					<td>{{$client->cuit}}</td>
				</tr>
				@endif
				@if(Auth::user()->permissionViewPurchase_Orders->state)
				<tr>
					<td colspan="2"><a class="btn btn-primary btn-block" href="{{asset('purchase_orders/'.$client->id)}}">Ver compras historicas</a></td>
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
				@foreach($client->contacts as $contact)
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
