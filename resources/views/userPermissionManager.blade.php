@extends('layouts.app')
@section('pageTitle')Gestor de permisos - @endsection
@section('scripts')
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
		<h1>Gestor de permisos de usuarios</h1>
@can('editPermissions')
<table class="table table-hover">
	<thead class="thead-dark">
		<tr>
			<th>Usuario</th>
			<th>E-mail</th>
			<th>Nivel de acceso</th>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $user)
		<tr>
			<td><a href="/user/{{ $user->id }}" target="_blank">{{$user->name}}</a></td>
			<td><a href="/user/{{ $user->id }}" target="_blank">{{$user->email}}</a></td>
			<td><a href="/user/{{ $user->id }}" target="_blank">{{$user->roles->first()->description}}</a></td>
		</tr>
		@endforeach
	</tbody>
</table>
@endcan
@endsection
