@extends('layouts.app')
@section('pageTitle'){{"Proveedores -"}} @endsection
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
		<h1>Proveedores</h1>
		@if((Auth::user()->permissionCreateSupplier->state))
		<a href="/createsupplier">Crear nuevo proveedor</a>
		@endif
		@if((Auth::user()->permissionViewHiddenSuppliers->state) OR (Auth::user()->permissionViewDisabledSuppliers->state) OR (Auth::user()->permissionViewDeletedSuppliers->state))
			@if($showAll)
		<a href="/suppliers" class="float-right">Mostrar solo proveedores habilitados</a>
			@else
		<a href="/suppliers/1" class="float-right">Mostrar todos los proveedores</a>
			@endif
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="suppliers"class="table">
		<thead>
			<tr>
				<th onclick="sortTable('suppliers', 0)">Nombre</th>
				<th onclick="sortTable('suppliers', 1)">Contacto</th>
				@if((Auth::user()->permissionCreateSupplier->state))
					<th></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodySuppliers">
	@foreach ($suppliers as $supplier)
		@if(($supplier->states->id == 1 AND Auth::user()->permissionViewSuppliers->state) OR ($supplier->states->id == 2 AND Auth::user()->permissionViewDisabledSuppliers->state AND $showAll) OR ($supplier->states->id == 3 AND Auth::user()->permissionViewHiddenSuppliers->state AND $showAll) OR ($supplier->states->id == 4 AND Auth::user()->permissionViewDeletedSuppliers->state AND $showAll))
			<tr class="background-color-row-state-{{$supplier->states->id}}">
				<td><a href="/supplier/{{$supplier['id']}}">{{$supplier['name']}}</a></td>
				<td>{{$supplier->contacts->first()->name ?? ''}} {{$supplier->contacts->first()->phone_number ?? $supplier->phone_number}}</td>
				@if((Auth::user()->permissionCreateSupplier->state))
					<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editSupplier({{$supplier->id}})" /></td>
				@endif
			</tr>
		@endif
	@endforeach
		</tbody>
	</table>
@endsection
