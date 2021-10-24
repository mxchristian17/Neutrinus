@extends('layouts.app')
@section('pageTitle'){{ "Crear elemento general -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var url = "{{ route('autocompleteelement.fetch') }}";
		var order_types = new Array();
		@foreach($order_types_data as $order_type)
			order_types[{{$order_type->id}}] = [{{$order_type->d_ext}}, {{$order_type->d_int}}, {{$order_type->side_a}}, {{$order_type->side_b}}, {{$order_type->large}}, {{$order_type->width}}, {{$order_type->thickness}}];

		@endforeach
	</script>
	<script type="application/javascript" src="{{ asset('js/Element.js')}}"></script>
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
		<h1 class="d-inline">Crear nuevo elemento general</h1><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los elementos generales son elementos que no pertenecen a ningun proyecto en especial, pero se encuentran definidos en el sistema para poder incorporarse a los distintos proyectos. <a href='/help/elements' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
		<br /><br />
		{!! Form::open(['url' => '/storeelement']) !!}
		{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
		{!! Form::text('name', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Nombre...'])) !!}
		{!! Form::Label('description', 'Descripción:', ['class' => 'control-label']) !!}
		{!! Form::textarea('description', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Descripción/Observaciones...'], ['rows' => '2'])) !!}
		{!! Form::Label('material_id', 'Material:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('material_id', $materials, null, ['class' => 'form-control']) !!}
		{!! Form::Label('order_type_id', 'Tipo de pedido:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('order_type_id', $order_types, null, ['class' => 'form-control']) !!}
		<div id="shared_material_container" class="custom-control custom-checkbox mt-2">
			{!! Form::checkbox('shared_material', 1, true, ['id' => 'shared_material', 'class' => 'custom-control-input']) !!}
			{!! Form::Label('shared_material', 'Material fragmentable', ['class' => 'custom-control-label']) !!}
			<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los materiales fragmentables son aquellos que salen de un material del cual se obtendrán varias piezas. Por ejemplo, si la pieza se fabricará en un torno de control numérico, se utilizará una barra de la cual salen muchas piezas. En este caso hablamos de un material fragmentable. Por el contrario hay casos en que cada pieza se fabrica de forma individual a base de un material que se pidió especificamente para esa pieza. Por ejemplo en el caso de que se vaya a fabricar una pieza de diámetro mayor al del pasaje de barra en un torno convencional. Siempre se considerará a la fragmentación respecto del largo del material."><img src="/images/helpIcon.png" alt="Ayuda" title="¿Qué es esto?" class="inline_icon pl-1 opacity-70 align-top"></a>
		</div>
		{!! Form::Label('d_ext', 'Diametro exterior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_ext', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('d_int', 'Diametro interior [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('d_int', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_a', 'Lado A [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_a', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('side_b', 'Lado B [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('side_b', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('large', 'Largo [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('large', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('width', 'Ancho [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('width', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('thickness', 'Espesor [mm]:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('thickness', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		{!! Form::Label('quantity_per_manufacturing_series', 'Cantidad mínima a fabricar por serie:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('quantity_per_manufacturing_series', 1, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '1'])) !!}
		@if(Auth::user()->permissionEditElementPrice->state)
		{!! Form::Label('additional_material_cost', 'Costo adicional de material:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('additional_material_cost', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		@endif
		{!! Form::Label('general_state_id', 'Estado inicial:', ['class' => 'control-label mt-2']) !!}
		{!! Form::select('general_state_id', $general_states, null, ['class' => 'form-control']) !!}
		<br />
		{!! Form::submit('Añadir elemento al proyecto', ['class' => 'form-control btn btn-primary']) !!}
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
