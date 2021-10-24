@extends('layouts.app')
@section('pageTitle'){{"Materiales -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Material.js')}}"></script>
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
		<h1>Materiales</h1>
		@if((Auth::user()->permissionCreateMaterial->state))
		<a href="/creatematerial">Crear nuevo material</a>
		@endif
		@if((Auth::user()->permissionViewHiddenMaterials->state) OR (Auth::user()->permissionViewDisabledMaterials->state) OR (Auth::user()->permissionViewDeletedMaterials->state))
			@if($showAll)
		<a href="/materials" class="float-right">Mostrar solo materiales habilitados</a>
			@else
		<a href="/materials/1" class="float-right">Mostrar todos los materiales</a>
			@endif
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="materials"class="table">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Sigla</th>
				<th>Peso especifico</th>
				<th>Autor</th>
				<th>Estado</th>
			@if((Auth::user()->permissionCreateMaterial->state))
				<th></th>
			@endif
			@if((Auth::user()->permissionDeleteMaterial->state))
				<th></th>
			@endif
			</tr>
		</thead>
		<tbody id="tbodyMaterials">
	@foreach ($materials as $material)
		@if(!($material->state_id == 2 AND !Auth::user()->permissionViewDisabledMaterials->state) AND !($material->state_id == 3 AND !Auth::user()->permissionViewHiddenMaterials->state) AND !($material->state_id == 4 AND !Auth::user()->permissionViewDeletedMaterials->state))
			<tr class="background-color-row-state-{{$material->state_id}}">
				<td><a href="#" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Descripción" data-content="{{$material->description}}">{{$material->name}}</a></td>
				<td>{{$material->initials}}</td>
				<td>{{$material->specific_weight}} kg/m<sup>3</sup></td>
				<td>By @can('seeUsersInformation')<a href="/user/{{$material->author->id}}">@endcan{{$material->author->name}}@can('seeUsersInformation')</a>@endcan</td>
				<td>{{$material->states->name}}</td>
			@if((Auth::user()->permissionCreateMaterial->state))
				<td><img src="/images/editIcon.png" class="table_icon" alt="Editar" title="Editar" onclick="editMaterial({{$material->id}})" /></td>
			@endif
			@if((Auth::user()->permissionDeleteMaterial->state))
				@if($material->state_id!=4)
				<td id="{{$material->id}}"><img src="/images/trashIcon.png" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteMaterial(this)" /></td>
				@else
				<td id="{{$material->id}}"><img src="/images/trashRedIcon.png" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteMaterial(this)" /></td>
				@endif
			@endif
			</tr>
		@endif
	@endforeach
		</tbody>
	</table>
	{{$materials->links()}}
@endsection
