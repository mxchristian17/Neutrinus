@extends('layouts.app')
@section('pageTitle'){{"Carro de compras -"}} @endsection
@section('scripts')
	<script type="text/javascript">
		var selectedSupplier;
	  var selectedSupplierName;
		var url = "{{ route('autocompletesupplier.fetch') }}";
	</script>
	<script type="application/javascript" src="{{ asset('js/Shopping_cart.js')}}"></script>
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
		<h1>Carro de compras</h1>
			<div class="autocomplete mb-3" style="width:100%;">
				{!! Form::Label('supplier', 'Proveedores:', ['class' => 'control-label mt-2 hide']) !!}
				{!! Form::text('supplier', null, array_merge(['class' => 'form-control input-lg'], ['autocomplete' => 'off'], ['placeholder' => 'Proveedores...'])) !!}
				<div id="supplierIdList" class="autocomplete-items">
				</div>
			</div>
		<div id="selectedSuppliers" class="mb-2">
		@foreach($cartSuppliers as $id => $cartSupplier)
			<div class="d-inline-flex btn-sm btn-success mr-2 mb-1" id="supplier_{{$id}}"><span class="d-inline-flex btn-sm text-white">{{$cartSupplier}}</span><span class="d-inline-flex btn btn-sm text-white" onclick="removeSupplierFromShoppingCart('{{$id}}')">X</span></div>
		@endforeach
		</div>
		<table class="table">
	   	<thead>
	       	<tr>
	           	<th>Elemento</th>
	           	<th>Cantidad</th>
	           	<th class="d-2-none">Precio unitario</th>
	           	<th>Subtotal</th>
							<th>IVA</th>
							<th>Total</th>
							<th></th>
	       	</tr>
	   	</thead>
	   	<tbody>
		@foreach(Cart::content() as $row)

	   		<tr id="r_{{$row->rowId}}">
	       		<td>
	           		<p><strong>{{$row->name}}</strong></p>
	           		<p>{{$row->options->has('size') ? $row->options->size : ''}}</p>
	       		</td>
	       		<td><input id="q_{{$row->rowId}}" class="form-control" onchange="shoppingCartItemUpdate('{{$row->id}}', $(this).val(), '{{$row->rowId}}');" type="text" value="{{$row->qty}}"></td>
	       		<td id="p_{{$row->rowId}}" class="d-2-none">${{round($row->price, 2)}}</td>
	       		<td id="st_{{$row->rowId}}">${{round($row->subtotal, 2)}}</td>
						<td id="t_{{$row->rowId}}">${{round($row->tax*$row->qty, 2)}}</td>
						<td id="tt_{{$row->rowId}}">${{round($row->total, 2)}}</td>
						<td><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="removeFromShoppingCart('{{$row->id}}', '{{$row->rowId}}', '{{$row->name}}')" /></td>
     		</tr>

   	@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>Subtotal estimado</td>
					<td id="total_subtotal">${{Cart::subtotal()}}</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>IVA estimado</td>
					<td>&nbsp;</td>
					<td id="total_tax">${{Cart::tax()}}</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>Total estimado</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td id="total_total">${{Cart::total()}}</td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<hr/>
		<div class="mt-3 mb-3 float-right">
			<a href="{{asset('/destroycart')}}" class="btn btn-outline-danger">Vaciar carro</a>
			<a href="{{asset('quotationrequest')}}" class="btn btn-primary">Solicitar cotización</a>
			<a href="{{asset('generatepurchaseorder/3')}}" class="btn btn-success">Enviar órden de compra</a>
		</div>
		<hr/>
@endsection
