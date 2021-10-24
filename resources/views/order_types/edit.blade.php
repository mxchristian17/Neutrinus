@extends('layouts.app')
@section('pageTitle'){{ "Editar tipo de pedido ".$ordertype->name." -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var formula = [@foreach($ordertype->original_formula as $formulaItem) '{{$formulaItem}}', @endforeach];
		var startingFormula = true;
	</script>
	<script type="application/javascript" src="{{ asset('js/Order_type.js')}}"></script>
@endsection
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
		<h1>Editar tipo de pedido {{$ordertype->name}}</h1>
		{!! Form::model($ordertype, [
		    'method' => 'POST',
		    'route' => ['updateordertype', $ordertype->id]
		]) !!}
		<div class="form-group">
				{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
				{!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
				@error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<div class="form-group">
			{!! Form::Label('description', 'Descripción:', ['class' => 'control-label']) !!}
			{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control'], ['style' => 'height:100px;'])) !!}
				@error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<h5 class="mt-2">Propiedades del tipo de pedido</h5>
		<table class="table mt-2">
			<tbody>
				<tr>
					<td>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('d_ext',0)}}
							{!! Form::checkbox('d_ext', 1 ?? old('d_ext'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'd_ext'], ['value' => 1])) !!}
							{!! Form::Label('d_ext', 'Diámetro exterior', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("d_ext")'])) !!}
						</div>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('d_int',0)}}
							{!! Form::checkbox('d_int', 1 ?? old('d_int'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'd_int'], ['value' => 1])) !!}
							{!! Form::Label('d_int', 'Diámetro interior', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("d_int")'])) !!}
						</div>
					</td>
					<td>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('side_a',0)}}
							{!! Form::checkbox('side_a', 1 ?? old('side_a'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'side_a'], ['value' => 1])) !!}
							{!! Form::Label('side_a', 'Lado A', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("side_a")'])) !!}
						</div>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('side_b',0)}}
							{!! Form::checkbox('side_b', 1 ?? old('side_b'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'side_b'], ['value' => 1])) !!}
							{!! Form::Label('side_b', 'Lado B', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("side_b")'])) !!}
						</div>
					</td>
					<td>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('large',0)}}
							{!! Form::checkbox('large', 1 ?? old('large'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'large'], ['value' => 1])) !!}
							{!! Form::Label('large', 'Largo', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("large")'])) !!}
						</div>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('width',0)}}
							{!! Form::checkbox('width', 1 ?? old('width'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'width'], ['value' => 1])) !!}
							{!! Form::Label('width', 'Ancho', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("width")'])) !!}
						</div>
					</td>
					<td>
						<div class="custom-control custom-checkbox">
							{{Form::hidden('thickness',0)}}
							{!! Form::checkbox('thickness', 1 ?? old('thickness'), null, array_merge(['class' => 'custom-control-input'], ['id' => 'thickness'], ['value' => 1])) !!}
							{!! Form::Label('thickness', 'Espesor', array_merge(['class' => 'custom-control-label'], ['onclick' => 'enableDisable("thickness")'])) !!}
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<h5 class="row justify-content-center">Generador de fórmula de volúmen</h5>
		<div class="row justify-content-center">
		  <div class="col-auto">
				{{ Form::text('formulaGen', null, array_merge(['id' => 'formula'], ['class' => 'form-control'], ['readonly'])) }}
				<table class="table w-auto">
					<tbody>
						<tr>
							<td class="p-2 td-formulaGenerator-hover" id="x2" onclick="formulaGenerator(this.id)" title="Elevado al cuadrado">x<sup>2</sup></td>
							<td class="p-2 td-formulaGenerator-hover" id="x3" onclick="formulaGenerator(this.id)" title="Elevado al cubo">x<sup>3</sup></td>
							<td class="p-2 td-formulaGenerator-hover" id="sqrx" onclick="formulaGenerator(this.id)" title="Raíz cuadrada">√x</td>
							<td class="p-2 td-formulaGenerator-hover" id="c" onclick="formulaGenerator(this.id)" title="Borrar todo">C</td>
							<td class="p-2 td-formulaGenerator-hover" id="backspace" onclick="formulaGenerator(this.id)" title="Borrar">⌫</td>
						</tr>
						<tr>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="flarge" onclick="formulaGenerator(this.id)" title="Largo">Largo</td>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fwidth" onclick="formulaGenerator(this.id)" title="Ancho">Ancho</td>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fthickness" onclick="formulaGenerator(this.id)" title="Espesor">Esp.</td>
							<td class="p-2 td-formulaGenerator-hover" id="openparentheses" onclick="formulaGenerator(this.id)">(</td>
							<td class="p-2 td-formulaGenerator-hover" id="closeparentheses" onclick="formulaGenerator(this.id)">)</td>
						</tr>
						<tr>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fd_ext" onclick="formulaGenerator(this.id)" title="Diámetro exterior">Øext</td>
							<td class="p-2 td-formulaGenerator-hover" id="1" onclick="formulaGenerator(this.id)">1</td>
							<td class="p-2 td-formulaGenerator-hover" id="2" onclick="formulaGenerator(this.id)">2</td>
							<td class="p-2 td-formulaGenerator-hover" id="3" onclick="formulaGenerator(this.id)">3</td>
							<td class="p-2 td-formulaGenerator-hover" id="add" onclick="formulaGenerator(this.id)">+</td>
						</tr>
						<tr>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fd_int" onclick="formulaGenerator(this.id)" title="Diámetro interior">Øint</td>
							<td class="p-2 td-formulaGenerator-hover" id="4" onclick="formulaGenerator(this.id)">4</td>
							<td class="p-2 td-formulaGenerator-hover" id="5" onclick="formulaGenerator(this.id)">5</td>
							<td class="p-2 td-formulaGenerator-hover" id="6" onclick="formulaGenerator(this.id)">6</td>
							<td class="p-2 td-formulaGenerator-hover" id="subtract" onclick="formulaGenerator(this.id)">-</td>
						</tr>
						<tr>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fside_a" onclick="formulaGenerator(this.id)" title="Lado A">LadoA</td>
							<td class="p-2 td-formulaGenerator-hover" id="7" onclick="formulaGenerator(this.id)">7</td>
							<td class="p-2 td-formulaGenerator-hover" id="8" onclick="formulaGenerator(this.id)">8</td>
							<td class="p-2 td-formulaGenerator-hover" id="9" onclick="formulaGenerator(this.id)">9</td>
							<td class="p-2 td-formulaGenerator-hover" id="multiply" onclick="formulaGenerator(this.id)">x</td>
						</tr>
						<tr>
							<td class="p-2 td-formulaGenerator-hover td-formulaGenerator-disabled" id="fside_b" onclick="formulaGenerator(this.id)" title="Lado B">LadoB</td>
							<td class="p-2 td-formulaGenerator-hover" id="pi" onclick="formulaGenerator(this.id)" title="Número PI = 3.1415...">π</td>
							<td class="p-2 td-formulaGenerator-hover" id="0" onclick="formulaGenerator(this.id)">0</td>
							<td class="p-2 td-formulaGenerator-hover" id="coma" onclick="formulaGenerator(this.id)" title="Coma">,</td>
							<td class="p-2 td-formulaGenerator-hover" id="divide" onclick="formulaGenerator(this.id)">/</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		{{ Form::hidden('formulaSend', null, ['id' => 'formulaSend']) }}
		@if ($errors->any())
				<div class="alert alert-danger">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
		{!! Form::Label('state_id', 'Estado', ['class' => 'control-label']) !!}
		{!! Form::select('state_id', $general_states, $ordertype->state_id, ['class' => 'form-control mb-3']) !!}
		{!! Form::submit('Actualizar tipo de pedido', array_merge(['class' => 'btn btn-primary'], ['onclick' => 'checkSubmit(event)'])) !!}
		<a class="btn btn-link" href="{{asset('/ordertypes')}}">Volver</a>
		{!! Form::close() !!}
@endsection
