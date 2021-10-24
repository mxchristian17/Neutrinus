@extends('layouts.app')
@section('pageTitle'){{ "Cargar precio de material -" }} @endsection
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
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Cargar precio de material</h1>
		{!! Form::open(array('url' => '/storematerialprice', 'id' => 'main-form')) !!}
		{!! Form::Label('suplier_id', 'Proveedor:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control']) !!}
		{!! Form::Label('material_id', 'Material:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('material_id', $materials, null, ['class' => 'form-control']) !!}
		{!! Form::Label('order_type_id', 'Tipo de pedido:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('order_type_id', $order_types, null, ['class' => 'form-control']) !!}
		{!! Form::Label('d_ext', 'Diametro exterior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_ext', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('d_int', 'Diametro interior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_int', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_a', 'Lado A [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_a', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_b', 'Lado B [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_b', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('width', 'Ancho [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('width', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('thickness', 'Espesor [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('thickness', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('price', 'Precio por kg[USD]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('price', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		<br />
		{!! Form::submit('Guardar', ['class' => 'form-control btn btn-primary']) !!}
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
