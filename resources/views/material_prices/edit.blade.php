@extends('layouts.app')
@section('pageTitle'){{ "Editar precio de material -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var url = "{{ route('checklogicprice') }}";
		var order_types = new Array();
		@foreach($order_types_data as $order_type)
			order_types[{{$order_type->id}}] = [{{$order_type->d_ext}}, {{$order_type->d_int}}, {{$order_type->side_a}}, {{$order_type->side_b}}, {{$order_type->large}}, {{$order_type->width}}, {{$order_type->thickness}}];
		@endforeach
		$(function () {
				$.ajaxSetup({
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
				});
		});
	</script>
	<script type="application/javascript" src="{{ asset('js/Material_price.js')}}"></script>
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
		<h1>Editar precio de material</h1>
		{!! Form::model($material_price, [
		    'method' => 'POST',
		    'route' => ['updatematerialprice', $material_price->id],
				'id' => 'main-form'
		]) !!}
		{!! Form::Label('suplier_id', 'Proveedor:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('supplier_id', $suppliers, old('supplier_id'), ['class' => 'form-control']) !!}
		{!! Form::Label('material_id', 'Material:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('material_id', $materials, old('material_id'), ['class' => 'form-control']) !!}
		{!! Form::Label('order_type_id', 'Tipo de pedido:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('order_type_id', $order_types, old('order_type_id'), ['class' => 'form-control']) !!}
		{!! Form::Label('d_ext', 'Diametro exterior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_ext', old('d_ext'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('d_int', 'Diametro interior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_int', old('d_int'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_a', 'Lado A [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_a', old('side_a'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_b', 'Lado B [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_b', old('side_b'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('width', 'Ancho [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('width', old('width'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('thickness', 'Espesor [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('thickness', old('thickness'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('price', 'Precio por kg[USD]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('price', old('price'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		<br />
		{!! Form::submit('Guardar cambios', ['class' => 'form-control btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset('/materialprices')}}">Volver</a>
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
