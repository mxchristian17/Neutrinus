@extends('layouts.app')
@section('pageTitle'){{"Clientes -"}} @endsection
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
		<h1>Clientes</h1>
		@if((Auth::user()->permissionCreateClient->state))
		<a href="/createclient">Crear nuevo cliente</a>
		@endif
		@if((Auth::user()->permissionViewHiddenClients->state) OR (Auth::user()->permissionViewDisabledClients->state) OR (Auth::user()->permissionViewDeletedClients->state))
			@if($showAll)
		<a href="/clients" class="float-right">Mostrar solo clientes habilitados</a>
			@else
		<a href="/clients/1" class="float-right">Mostrar todos los clientes</a>
			@endif
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="clients"class="table">
		<thead>
			<tr>
				<th onclick="sortTable('clients', 0)">Nombre</th>
				<th onclick="sortTable('clients', 1)">Contacto</th>
				@if((Auth::user()->permissionCreateClient->state))
					<th></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodyClients">
	@foreach ($clients as $client)
		@if(($client->states->id == 1 AND Auth::user()->permissionViewClients->state) OR ($client->states->id == 2 AND Auth::user()->permissionViewDisabledClients->state AND $showAll) OR ($client->states->id == 3 AND Auth::user()->permissionViewHiddenClients->state AND $showAll) OR ($client->states->id == 4 AND Auth::user()->permissionViewDeletedClients->state AND $showAll))
			<tr class="background-color-row-state-{{$client->states->id}}">
				<td><a href="/client/{{$client['id']}}">{{$client['name']}}</a></td>
				<td>{{$client->contacts->first()->name ?? ''}} {{$client->contacts->first()->phone_number ?? $client->phone_number}}</td>
				@if((Auth::user()->permissionCreateClient->state))
					<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editClient({{$client->id}})" /></td>
				@endif
			</tr>
		@endif
	@endforeach
		</tbody>
	</table>
@endsection
