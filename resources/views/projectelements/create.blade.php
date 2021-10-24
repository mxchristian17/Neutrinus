@extends('layouts.app')
@section('pageTitle'){{ "Añadir elemento a subconjunto -" }} @endsection
@section('scripts')
	<script type="text/javascript">
		var elementsUrl = "{{ route('autocompleteelement.fetch') }}";
		@if((Auth::user()->permissionCreateElement->state))
		var order_types = new Array();
		@foreach($order_types_data as $order_type)
			order_types[{{$order_type->id}}] = [{{$order_type->d_ext}}, {{$order_type->d_int}}, {{$order_type->side_a}}, {{$order_type->side_b}}, {{$order_type->large}}, {{$order_type->width}}, {{$order_type->thickness}}];

		@endforeach
		@endif

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
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Añadir elemento a {{$project->name}}</h1>
		@if((Auth::user()->permissionCreateElement->state))
		<a class="btn btn-primary" id="newElementDefinitionButton" href="#" onclick="newElement()">Crear nuevo elemento general</a>
		@endif
		{!! Form::open(['url' => '/storeprojectelement']) !!}
		<div class="autocomplete" style="width:100%;">
			{!! Form::Label('element', 'Elemento:', array_merge(['class' => 'control-label'], ['class' => 'mt-2'])) !!}
			{!! Form::text('element', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Nombre o código del elemento...'])) !!}
			<small id="elementHelp" class="form-text text-muted">Complete el campo con el nombre o código único del elemento que busca y haga la seleccion desde el selector con un click o dando a la tecla Enter.</small>
			<div id="elementIdListB" class="autocomplete-items">
	    </div>
		</div>
		<br />

		@if((Auth::user()->permissionCreateElement->state))
		<div id="newElementDefinition" class="form-group">
		{!! Form::Label('name', 'Nombre:', ['class' => 'control-label']) !!}
		{!! Form::text('name', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Nombre...'])) !!}
        @if($errors->has('name'))
				<div class="alert alert-danger">
					<ul>
		        <li>
		            {{$errors->first('name')}}
		        </li>
					</ul>
				</div>
        @endif
		{!! Form::Label('description', 'Descripción:', ['class' => 'control-label']) !!}
		{!! Form::textarea('description', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Descripción/Observaciones...'], ['rows' => '2'])) !!}
		{!! Form::Label('material_id', 'Material:', ['class' => 'control-label']) !!}
		{!! Form::select('material_id', $materials, null, ['class' => 'form-control']) !!}
		{!! Form::Label('order_type_id', 'Tipo de pedido:', ['class' => 'control-label']) !!}
		{!! Form::select('order_type_id', $order_types, null, ['class' => 'form-control']) !!}
		{!! Form::Label('d_ext', 'Diametro exterior [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('d_ext', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('d_int', 'Diametro interior [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('d_int', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('side_a', 'Lado A [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('side_a', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('side_b', 'Lado B [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('side_b', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('large', 'Largo [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('large', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('width', 'Ancho [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('width', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('thickness', 'Espesor [mm]:', ['class' => 'control-label']) !!}
		{!! Form::number('thickness', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'])) !!}
		{!! Form::Label('quantity_per_manufacturing_series', 'Cantidad mínima a fabricar por serie:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('quantity_per_manufacturing_series', 1, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '1'], ['min' => '1'])) !!}
		@if(Auth::user()->permissionEditElementPrice->state)
		{!! Form::Label('additional_material_cost', 'Costo adicional de material:', ['class' => 'control-label mt-2']) !!}
		{!! Form::number('additional_material_cost', 0, array_merge(['class' => 'form-control'], ['autocomplete' => 'off'], ['step' => '0.01'], ['min' => '0'])) !!}
		@endif
		{!! Form::hidden('newType', 0, array_merge(['class' => 'form-control'], ['id' => 'newType'])) !!}
		</div>
		@endif

		{{-- !! Form::Label('subset', 'Subconjunto:', ['class' => 'control-label']) !!--}}
		{{-- !! Form::select('subset', $subsets, null, ['class' => 'form-control']) !!--}}
		{!! Form::Label('quantity', 'Cantidad:', ['class' => 'control-label']) !!}
		{!! Form::number('quantity', 1, ['class' => 'form-control']) !!}
		{!! Form::Label('purchase_order', 'Órden para la compra:', ['class' => 'control-label']) !!}
		{!! Form::number('purchase_order', 1, ['class' => 'form-control']) !!}
		{!! Form::Label('manufacturing_order', 'Órden para la fabricación:', ['class' => 'control-label']) !!}
		{!! Form::number('manufacturing_order', 1, ['class' => 'form-control']) !!}
		{!! Form::hidden('project_id', $project->id) !!}
		{!! Form::hidden('subset_id', $subset) !!}
		{!! Form::hidden('author_id', Auth::user()->id) !!}
		{!! Form::hidden('updater_id', Auth::user()->id) !!}
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
