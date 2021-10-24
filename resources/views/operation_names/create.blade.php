@extends('layouts.app')
@section('pageTitle'){{ "Crear operación -" }} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Operation.js')}}"></script>
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
		<h1 class="d-inline">Crear nueva ruta</h1><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Las rutas son los posibles caminos que recorrerá un elemento hasta estar dispuesto para la venta. En el caso de ser un elemento que se fabrica en la planta, un tipo de ruta puede ser corte a serrucho sin fin, otro puede ser Torno, otro pintura, etc... <a href='/help/operations' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
		{!! Form::open(['url' => '/storeoperation_name']) !!}
		{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
		{!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
		{!! Form::Label('description', 'Descripción:', ['class' => 'control-label mt-2']) !!}
		{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control'], ['style' => 'height:100px;'])) !!}
		{!! Form::Label('usd_for_hour', 'Costo por hora en USD:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('usd_for_hour', old('usd_for_hour') ?? 0, array_merge(['class' => 'form-control'], ['step' => '0.01'])) !!}
		{!! Form::Label('state_id', 'Estado', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('state_id', $general_states, 1, ['class' => 'form-control mb-3']) !!}
		@if ($errors->any())
				<div class="alert alert-danger mt-2">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::submit('Añadir ruta', array_merge(['class' => 'btn btn-primary'], ['onclick' => 'checkSubmit(event)'])) !!}
		{!! Form::close() !!}
@endsection
