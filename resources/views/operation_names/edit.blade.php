@extends('layouts.app')
@section('pageTitle'){{ "Editar ruta ".$operation_name->name." -" }} @endsection
@section('content')
	{{-- @include('alerts.errors') --}}
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Editar ruta {{$operation_name->name}}</h1>
		{!! Form::model($operation_name, [
		    'method' => 'POST',
		    'route' => ['updateoperationname', $operation_name->id]
		]) !!}
		{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
		{!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
		{!! Form::Label('description', 'Descripción:', ['class' => 'control-label mt-2']) !!}
		{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control'], ['style' => 'height:100px;'])) !!}
		{!! Form::Label('usd_for_hour', 'Costo por hora en USD:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('usd_for_hour', old('usd_for_hour') ?? 0, array_merge(['class' => 'form-control'], ['step' => '0.01'])) !!}
		{!! Form::Label('state_id', 'Estado', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('state_id', $general_states, $operation_name->state_id, ['class' => 'form-control mb-3']) !!}
		@if ($errors->any())
				<div class="alert alert-danger mt-2">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::submit('Actualizar ruta', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset('/operation_names')}}">Volver</a>
		{!! Form::close() !!}
@endsection
