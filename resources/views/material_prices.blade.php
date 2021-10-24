@extends('layouts.app')
@section('pageTitle'){{"Precio de materiales -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Material_price.js')}}"></script>
	<script>
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

			@else

			@endauth
		</div>
		@endif
		<h2>Precio de materiales no normalizados</h2>
		@if((Auth::user()->permissionCreateMaterialPrice->state))
		<a href="/creatematerialprice">Cargar nuevo precio</a>
		@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="materials"class="table">
		<thead>
			<tr>
				<th class="max-col-char">Material</th>
				<th class="max-col-char">Tipo de pedido</th>
				<th class="max-col-char">Øext</th>
				<th class="max-col-char">Øint</th>
				<th class="max-col-char">LadoA</th>
				<th class="max-col-char">LadoB</th>
				<th class="max-col-char">Ancho</th>
				<th class="max-col-char">Esp</th>
			@if((Auth::user()->permissionViewMaterialPrices->state))
				<th class="max-col-char">USD/Kg</th>
			@endif
			@if((Auth::user()->permissionViewSuppliers->state))
				<th class="max-col-char">Proveedor</th>
			@endif
				<th class="max-col-char">Autor</th>
				<th class="d-1-none">Actualizado</th>
			@if((Auth::user()->permissionCreateMaterialPrice->state))
				<th class="d-2-none"></th>
				<th class="d-2-none"></th>
			@endif
			</tr>
		</thead>
		<tbody id="tbodyElements">
	@foreach ($prices as $price)
			<tr class="background-color-row-state-{{-1*$price->enabled+2}}">
				<td class="max-col-char" title="{{$price->material->name}}">{{$price->material->initials}}</td>
				<td class="max-col-char" title="{{$price->order_type->name}}">{{$price->order_type->name}}</td>
				<td class="max-col-char" title="{{$price->d_ext}}">@if($price->d_ext > 0){{$price->d_ext}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
				<td class="max-col-char" title="{{$price->d_int}}">@if($price->d_int > 0){{$price->d_int}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
				<td class="max-col-char" title="{{$price->side_a}}">@if($price->side_a > 0){{$price->side_a}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
				<td class="max-col-char" title="{{$price->side_b}}">@if($price->side_b > 0){{$price->side_b}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
				<td class="max-col-char" title="{{$price->width}}">@if($price->width > 0){{$price->width}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
				<td class="max-col-char" title="{{$price->thickness}}">@if($price->thickness > 0){{$price->thickness}}<span class="d-1-none"><small class="text-secondary">mm</small></span>@endif</td>
			@if((Auth::user()->permissionViewMaterialPrices->state))
				<td class="text-right max-col-char" title="{{$price->price}}">{{$price->price}}<span class="d-1-none"><small class="text-secondary">USD/Kg</small></span></td>
			@endif
			@if((Auth::user()->permissionViewSuppliers->state))
				<td class="max-col-char" title="{{$price->supplier->name}}">{{$price->supplier->name}}</td>
			@endif
				<td class="max-col-char">@can('seeUsersInformation')<a href="/user/{{$price->author->id}}">@endcan{{$price->author->name}}@can('seeUsersInformation')</a>@endcan</td>
				<td class="d-1-none">{{$price->updated_at->diffForHumans()}}
					@if($price->updated_at < $outOfDate)
					<a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Atención" data-content="El precio de este material es muy antiguo y pudo haber presentado cambios significativos. Por favor actualice el valor."><img src="/images/warningIcon.png" alt="Atención" class="inline_icon pl-1 opacity-70"></a>
					@endif
				</td>
			@if((Auth::user()->permissionCreateMaterialPrice->state))
				<td class="d-2-none"><img src="/images/editIcon.png" class="table_icon" alt="Editar" title="Editar" onclick="editPrice({{$price->id}})" /></td>
			@endif
			@if((Auth::user()->permissionCreateMaterialPrice->state))
				<td class="d-2-none" id="{{$price->id}}"><img src="/images/trashIcon.png" class="table_icon" alt="Borrar" title="Borrar" onclick="deletePrice(this)" /></td>
			@endif
			</tr>
	@endforeach
		</tbody>
	</table>
{{$prices->links()}}
@endsection
