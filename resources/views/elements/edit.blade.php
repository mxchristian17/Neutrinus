@extends('layouts.app')
@section('pageTitle'){{ "Editar material ".$element->name." -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var url = "{{ route('autocompleteelement.fetch') }}";
		var editing_element = true;
		var order_types = new Array();
		@foreach($order_types_data as $order_type)
			order_types[{{$order_type->id}}] = [{{$order_type->d_ext}}, {{$order_type->d_int}}, {{$order_type->side_a}}, {{$order_type->side_b}}, {{$order_type->large}}, {{$order_type->width}}, {{$order_type->thickness}}];

		@endforeach
	</script>
	<script type="application/javascript" src="{{ asset('js/Element.js')}}"></script>
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
		<h1>Editar {{$element->name}}</h1>
		<div class="form-text text-danger">Atención: La edición de este elemento afectará a todos los proyectos en los que se encuentre referenciado</div>
		{!! Form::model($element, [
		    'method' => 'POST',
		    'route' => ['updateelement', $element->id],
				'class' => 'mt-2'
		]) !!}
		<div class="form-group">
			{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
			{!! Form::text('name', old('name'), array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Nombre...'])) !!}
	        @if($errors->has('name'))
					<div class="alert alert-danger">
						<ul>
			        <li>
			            {{$errors->first('name')}}
			        </li>
						</ul>
					</div>
	        @endif
		</div>
		<div class="form-group">
			{!! Form::Label('description', 'Descripción:', ['class' => 'control-label']) !!}
			{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Descripción/observaciones...'], ['rows' => '2'])) !!}
	        @if($errors->has('description'))
					<div class="alert alert-danger">
						<ul>
			        <li>
			            {{$errors->first('description')}}
			        </li>
						</ul>
					</div>
	        @endif
		</div>
		<div class="form-group">
		{!! Form::Label('material_id', 'Material:', ['class' => 'control-label']) !!}
		{!! Form::select('material_id', $materials, null, ['class' => 'form-control']) !!}
			@error('material_id')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
			{!! Form::Label('order_type_id', 'Tipo de pedido:', ['class' => 'control-label']) !!}
			{!! Form::select('order_type_id', $order_types, null, ['class' => 'form-control']) !!}
			@error('order_type')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div id="shared_material_container" class="custom-control custom-checkbox mt-2">
			{!! Form::checkbox('shared_material', 1, old('shared_material'), ['id' => 'shared_material', 'class' => 'custom-control-input']) !!}
			{!! Form::Label('shared_material', 'Material fragmentable', ['class' => 'custom-control-label']) !!}
			<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los materiales fragmentables son aquellos que salen de un material del cual se obtendrán varias piezas. Por ejemplo, si la pieza se fabricará en un torno de control numérico, se utilizará una barra de la cual salen muchas piezas. En este caso hablamos de un material fragmentable. Por el contrario hay casos en que cada pieza se fabrica de forma individual a base de un material que se pidió especificamente para esa pieza. Por ejemplo en el caso de que se vaya a fabricar una pieza de diámetro mayor al del pasaje de barra en un torno convencional. Siempre se considerará a la fragmentación respecto del largo del material."><img src="/images/helpIcon.png" alt="Ayuda" title="¿Qué es esto?" class="inline_icon pl-1 opacity-70 align-top"></a>
		</div>
		<div class="form-group">
			{!! Form::Label('d_ext', 'Diametro exterior [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('d_ext', old('d_ext'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('d_int', 'Diametro interior [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('d_int', old('d_int'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('side_a', 'Lado A [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('side_a', old('side_a'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('side_b', 'Lado B [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('side_b', old('side_b'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('large', 'Largo [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('large', old('large'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('width', 'Ancho [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('width', old('width'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('thickness', 'Espesor [mm]:', ['class' => 'control-label']) !!}
			{!! Form::number('thickness', old('thickness'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			{!! Form::Label('quantity_per_manufacturing_series', 'Cantidad mínima a fabricar por serie:', ['class' => 'control-label mt-2']) !!}
			{!! Form::number('quantity_per_manufacturing_series', old('quantity_per_manufacturing_series'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '1'])) !!}
			@if(Auth::user()->permissionEditElementPrice->state)
			{!! Form::Label('additional_material_cost', 'Costo adicional de material:', ['class' => 'control-label mt-2']) !!}
			{!! Form::number('additional_material_cost', old('additional_material_cost'), array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
			@endif
		</div>
		<div class="form-group">
			{!! Form::hidden('prev', URL::previous(), ['class' => 'form-control']) !!}
			{!! Form::Label('general_state_id', 'Estado inicial:', ['class' => 'control-label']) !!}
			{!! Form::select('general_state_id', $general_states, null, ['class' => 'form-control']) !!}
			@error('general_state_id')
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
		{!! Form::submit('Actualizar elemento', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{ URL::previous() }}">Volver</a>
		{!! Form::close() !!}
@endsection
