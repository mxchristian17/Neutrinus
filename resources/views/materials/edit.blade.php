@extends('layouts.app')
@section('pageTitle'){{ "Editar material ".$material->name." -" }} @endsection
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
		<h1>Editar material {{$material->name}}</h1>
		{!! Form::model($material, [
		    'method' => 'POST',
		    'route' => ['updatematerial', $material->id]
		]) !!}
		<div class="form-group">
		    {!! Form::label('name', 'Nombre', ['class' => 'control-label']) !!}
		    {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
				@error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<div class="form-group">
		    {!! Form::label('initials', 'Sigla que define al material', ['class' => 'control-label']) !!} <small>Máximo 8 caracteres</small>
		    {!! Form::text('initials', old('initials'), ['class' => 'form-control']) !!}
				@error('initials')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<div class="form-group">
		    {!! Form::label('description', 'Descripción', ['class' => 'control-label']) !!}
		    {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
				@error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<div class="form-group">
		    {!! Form::label('specific_weight', 'Peso específico', ['class' => 'control-label']) !!} [Kg/m<sup>3</sup>]
		    {!! Form::text('specific_weight', old('specific_weight'), ['class' => 'form-control']) !!}
				@error('specific_weight')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		{!! Form::Label('state_id', 'Estado', ['class' => 'control-label']) !!}
		{!! Form::select('state_id', $general_states, $material->state_id, ['class' => 'form-control mb-3']) !!}
		@if ($errors->any())
				<div class="alert alert-danger">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::submit('Actualizar material', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset('/materials')}}">Volver</a>
		{!! Form::close() !!}
@endsection
