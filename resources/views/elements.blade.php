@extends('layouts.app')
@section('pageTitle'){{"Elementos -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Element.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
		  $("#tableSearchInput").on("keyup", function() {
		    var value = $(this).val().toLowerCase();
		    $("#tbodyElements tr").filter(function() {
		      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		    });
		  });
		});
	</script>
@endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
				<h1>Elementos</h1>
			@else

			@endauth
		</div>
		@endif
		@if((Auth::user()->permissionCreateElement->state))
	<a href="/createelement">Crear nuevo elemento</a>
		@endif
		@if(((Auth::user()->permissionViewHiddenElements->state) OR (Auth::user()->permissionViewDisabledElements->state) OR (Auth::user()->permissionViewDeletedElements->state)) AND $optionShowAll == 1)
			@if($showAll)
		<a href="/elements" class="btn btn-link p-1 float-right">Mostrar solo elementos habilitados</a>
			@else
		<a href="/elements/1" class="btn btn-link p-1 float-right">Mostrar todos los elementos</a>
			@endif
		@elseif($optionShowAll == 0)
		<hr/>
		<span class="text-secondary">Mostrando resultados para: "{{$query}}"</span><br />
		<a href="{{asset('/elements')}}">Volver a ver todos los elementos</a>
		<hr/>
		@endif
		<form id="elementSearchForm" action="{{asset('/searchelement')}}" method="GET" role="search">
        {{ csrf_field() }}
        <div class="input-group">
            <input id="search_element_input" type="text" class="form-control" name="query" placeholder="Elemento a buscar..." autocomplete="off">
						<span class="input-group-btn">
                <button type="submit" class="btn btn-outline-dark">
                    Buscar
                </button>
            </span>
						<div id="elementIdList" class="autocomplete-items">
				    </div>
        </div>
    </form>
	<table id="elements"class="table">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Tipo</th>
				<th>Estado</th>
				<th>Autor</th>
				@if(Auth::user()->permissionDeleteElement->state)
				<th></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodyElements">
	@foreach ($elements as $element)
			<tr>
				<td><a href="/element/{{$element['id']}}">{{$element['name']}}</a></td>
				<td>{{$element['type']}}</td>
				<td><span  class="btn-sm {{classForGeneralStateTitle($element->general_state_id, true)}}">{{$element->general_state->name}}</span></td>
				<td><a href="/user/{{$element->author->id}}">{{$element->author->name}}</a></td>
				@if(Auth::user()->permissionDeleteElement->state)
					@if($element->general_state->id!=4)
				<td id="{{$element->id}}"><img src="{{asset('images/trashIcon.png')}}" class="table_icon" alt="Borrar" onclick="deleteElement(this)" /></td>
					@else
				<td id="{{$element->id}}"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteElement(this,'none')" /></td>
					@endif
				@endif
			</tr>
	@endforeach
		</tbody>
	</table>
	{!! $elements->appends(request()->input())->links() !!}
@endsection
