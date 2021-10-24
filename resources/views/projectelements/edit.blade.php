@extends('layouts.app')
@section('pageTitle'){{ "Editar material ".$projectelement->element->name." -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var url = "{{ route('autocompleteelement.fetch') }}";
		var origName = "{{$projectelement->element->name}} ({{$projectelement->element->nro}}-{{$projectelement->element->add}})";

		$(document).ready(function(){
		 var li = $('.elementSelectable');
		 var liSelected;
		 $('#element').focus();
		 $('#element').keyup(function(e){
						if((e.which != 40) && (e.which != 38) && (e.which != 13) && (e.which != 27)){
							var query = $(this).val();
							if(query != '')
							{
							 var _token = $('input[name="_token"]').val();
							 $.ajax({
								url: elementsUrl,
								method:"POST",
								data:{query:query, _token:_token},
								success:function(data){
								 $('#elementIdListB').fadeIn();
								 $('#elementIdListB').html(data);
								 li = $('.elementSelectable');
								 liSelected = false;
								}
							 });
						 }else{
							 $('#elementIdListB').fadeOut();
						 }
					 }
				});

				$(document).on('click', '.searchElementLi', function(){
						$('#element').val($(this).text());
						$('#elementIdListB').fadeOut();
						$('#elementSearchForm').submit();
				});

				$(window).keydown(function(e){
						if(e.which === 40){
								if(liSelected){
										liSelected.removeClass('elementSelected');
										next = liSelected.next();
										if(next.length > 0){
												liSelected = next.addClass('elementSelected');
										}else{
												liSelected = li.eq(0).addClass('elementSelected');
										}
								}else{
										liSelected = li.eq(0).addClass('elementSelected');
								}
						}else if(e.which === 38){
								if(liSelected){
										liSelected.removeClass('elementSelected');
										next = liSelected.prev();
										if(next.length > 0){
												liSelected = next.addClass('elementSelected');
										}else{
												liSelected = li.last().addClass('elementSelected');
										}
								}else{
										liSelected = li.last().addClass('elementSelected');
								}
						}else if(e.which === 13){
								if(liSelected){
									$('#element').val(liSelected.text());
									$('#elementIdListB').fadeOut();
									e.preventDefault();
									//$('#elementSearchForm').submit();
								}
						}else if(e.which === 27){
									$('#elementIdListB').fadeOut();
						}

						if($('.elementSelected').position())
						{
							var $container = $('#elementSelector'),
							$scrollTo = $('.elementSelected');
							$container.scrollTop(
								$scrollTo.offset().top - $container.offset().top + $container.scrollTop()-100
							);
						}

				});
			});
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
		<h1>Editar {{$projectelement->element->name}}<small> de {{$projectelement->project->name}}</small></h1>
		@if($projectelement->element->general_state_id < 4)
		<a class="btn btn-primary" href="{{asset('/editelement/'.$projectelement->element->id)}}">Editar parámetros generales del elemento</a>
		<small class="form-text text-muted">(nombre, material, dimensiones, etc...)</small>
		@endif
		{!! Form::model($projectelement, [
		    'method' => 'POST',
		    'route' => ['updateprojectelement', $projectelement->id],
				'class' => 'mt-2'
		]) !!}
		<div class="form-group">
			<div class="autocomplete" style="width:100%;">
				{!! Form::Label('element', 'Elemento general de partida:', ['class' => 'control-label']) !!}
				{!! Form::text('element', old('elementDesc') ?? "", array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Nombre o código del elemento...'])) !!}
				<small id="elementHelp" class="form-text text-muted">Complete el campo con el nombre o código único del elemento que busca y haga la seleccion desde el selector con un click o dando a la tecla Enter.</small>
				<div id="elementIdListB" class="autocomplete-items">
		    </div>
			</div>
			@error('element')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
		{!! Form::Label('subset', 'Subconjunto', ['class' => 'control-label']) !!}
		{!! Form::select('subset', $subsets, $projectelement->subset_id, ['class' => 'form-control mb-3']) !!}
			@error('subset')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
		{!! Form::Label('quantity', 'Cantidad:', ['class' => 'control-label']) !!}
		{!! Form::number('quantity', old('quantity'), ['class' => 'form-control']) !!}
			@error('quantity')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
		{!! Form::Label('purchase_order', 'Órden para la compra:', ['class' => 'control-label']) !!}
		{!! Form::number('purchase_order', old('purchase_order'), ['class' => 'form-control']) !!}
			@error('purchase_order')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
		{!! Form::Label('manufacturing_order', 'Órden para la fabricación:', ['class' => 'control-label']) !!}
		{!! Form::number('manufacturing_order', old('manufacturing_order'), ['class' => 'form-control']) !!}
			@error('manufacturing_order')
					<div class="invalid-feedback">{{ $message }}</div>
			@enderror
		</div>
		<div class="form-group">
		{!! Form::Label('specific_state_id', 'Estado', ['class' => 'control-label']) !!}
		{!! Form::select('specific_state_id', $general_states, $projectelement->specific_state_id, ['class' => 'form-control mb-3']) !!}
			@error('specific_state_id')
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
		<a class="btn btn-link" href="{{ asset("/project/$projectelement->project_id") }}">Volver</a>
		{!! Form::close() !!}
@endsection
