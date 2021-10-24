@extends('layouts.app')
@section('pageTitle'){{ "Editar operación -" }} @endsection
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
		<h1 class="d-inline">Editar operación</h1><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Las rutas son los posibles caminos que recorrerá un elemento hasta estar dispuesto para la venta. En el caso de ser un elemento que se fabrica en la planta, un tipo de ruta puede ser corte a serrucho sin fin, otro puede ser Torno, otro pintura, etc... <a href='/help/operations' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
		{!! Form::model($operation, [
		    'method' => 'POST',
		    'route' => ['updateoperation', $operation->id]
		]) !!}
		<div class="form-row">
			<div class="col">
				{!! Form::Label('operation_name_id', 'Ruta:', ['class' => 'control-label mt-2']) !!}
				{!! Form::select('operation_name_id', $operation_names, null, ['class' => 'form-control']) !!}
			</div>
			<div class="col">
				{!! Form::Label('order', 'Ubicado luego de:', ['class' => 'control-label mt-2']) !!}
				{!! Form::select('order', $operations_order, null, ['class' => 'form-control']) !!}
			</div>
		</div>
		<div class="form-row">
			<div class="col">
				{!! Form::Label('preparation_time', 'Tiempo de preparación [min]:', ['class' => 'control-label mt-2']) !!}
				{!! Form::number('preparation_time', old('preparation_time'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '0'])) !!}
			</div>
			<div class="col">
				{!! Form::Label('manufacturing_time', 'Tiempo de fabricación [min]:', ['class' => 'control-label mt-2']) !!}
				{!! Form::number('manufacturing_time', old('manufacturing_time'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '0'])) !!}
			</div>
		</div>
		<div class="form-row">
			<div class="col">
				{!! Form::Label('cnc_program', 'Programa:', ['class' => 'control-label mt-2']) !!}
				{!! Form::text('cnc_program', old('cnc_program'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'])) !!}
			</div>
			<div class="col">
				{!! Form::Label('operation_state_id', 'Estado:', ['class' => 'control-label mt-2']) !!}
				{!! Form::select('operation_state_id', $operation_states, null, ['class' => 'form-control']) !!}
			</div>
		</div>
		{!! Form::Label('observation', 'Observaciones de ruta:', ['class' => 'control-label']) !!}
		{!! Form::textarea('observation', old('observation'), array_merge(['class' => 'form-control'], ['rows' => '2'])) !!}
		{{ Form::hidden('element_id', $element->id) }}
		{{ Form::hidden('url', url()->previous()) }}
		{!! Form::submit('Modificar ruta', array_merge(['class' => 'btn btn-primary mt-2 mb-2'], ['onclick' => 'checkSubmit(event)'])) !!}
		<a class="btn btn-link" href="{{url()->previous()}}">Volver</a>
		@if ($errors->any())
				<div class="alert alert-danger mt-2">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::close() !!}
@endsection
