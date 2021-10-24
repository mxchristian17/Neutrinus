@extends('layouts.app')
@section('pageTitle'){{ "Crear subconjunto -" }} @endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Añadir subconjunto a {{$project->name}}</h1>
		{!! Form::open(['url' => '/storesubset']) !!}
		{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
		{!! Form::text('name', null, array_merge(['class' => 'form-control'], ['placeholder' => 'Nombre del subconjunto...'])) !!}
		{!! Form::select('state_id', $states, 1,['class'=>'form-control']) !!}
		{!! Form::hidden('subset_number', $subset) !!}
		{!! Form::hidden('project_id', $project->id) !!}
		{!! Form::hidden('author_id', Auth::user()->id) !!}
		{!! Form::hidden('updater_id', Auth::user()->id) !!}
		<br />
		{!! Form::submit('Añadir subconjunto al proyecto', ['class' => 'form-control btn btn-primary']) !!}
		@if ($errors->any())
				<div class="alert alert-danger">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::close() !!}
@endsection
