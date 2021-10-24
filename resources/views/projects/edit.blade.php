@extends('layouts.app')
@section('pageTitle'){{ "Editar proyecto ".$project->name." -" }} @endsection
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
		<h1>Editar proyecto {{$project->name}}</h1>
		{!! Form::model($project, [
		    'method' => 'POST',
		    'route' => ['updateproject', $project->id]
		]) !!}
		<div class="form-group">
		    {!! Form::label('name', 'Nombre', ['class' => 'control-label']) !!}
		    {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
				@error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		@if ($errors->any())
				<div class="alert alert-danger">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::Label('type', 'Tipo de proyecto', ['class' => 'control-label']) !!}
		{!! Form::select('type', $projecttypes, $project->projecttypes->id, ['class' => 'form-control mb-3']) !!}
		{!! Form::Label('state_id', 'Estado', ['class' => 'control-label']) !!}
		{!! Form::select('state_id', $general_states, $project->state_id, ['class' => 'form-control mb-3']) !!}
		{!! Form::submit('Actualizar proyecto', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset('/projects')}}">Volver</a>
		{!! Form::close() !!}
@endsection
