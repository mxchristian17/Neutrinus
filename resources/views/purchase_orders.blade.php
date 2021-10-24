@extends('layouts.app')
@section('pageTitle'){{"Seguimiento de compras -"}} @if($supplier){{$supplier->name}} - @endif @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Purchase.js')}}"></script>
	<script type="text/javascript">
		var orders = { @foreach ($purchase_orders as $purchase_order){{$purchase_order->id}}:{{$purchase_order->order_number}} @if(!$loop->last), @endif @endforeach };
		var orders_status = { @foreach ($purchase_orders as $purchase_order){{$purchase_order->id}}:{{$purchase_order->status}} @if(!$loop->last), @endif @endforeach }
	</script>
@endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
				<h1>Seguimiento de compras @if($supplier)de {{$supplier->name}}@endif</h1>
			@else

			@endauth
		</div>
		@endif
	<input class="form-control d-print-none" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="purchase_orders" class="table table-hover">
		<thead class="thead-dark">
			<tr>
				<th onclick="sortTable('purchase_orders', 0)">Código</th>
				@if((Auth::user()->permissionViewSuppliers->state))
				<th onclick="sortTable('purchase_orders', 1)">Proveedor</th>
				@endif
				<th class="d-2-none">Autor</th>
				<th>Estado</th>
				@if((Auth::user()->permissionViewPurchase_OrderPrices->state))
				<th class="d-3-none">Cotización</th>
				@endif
				@if(Auth::user()->permissionViewPurchase_Orders->state AND Auth::user()->permissionViewPurchase_OrderPrices->state)
				<th class="d-4-none"></th>
				@endif
				@if(Auth::user()->permissionCreatePurchase_order->state)
				<th class="text-center d-4-none"></th>
				@endif
				@if(Auth::user()->permissionDeletePurchase_order->state)
				<th class="text-center d-4-none"></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodyPurchase_orders">
	@foreach ($purchase_orders as $purchase_order)
		<tr class="row-purchase-status-{{$purchase_order->status}}">
			<td id="1_{{$purchase_order->id}}"><a id="p_btn_{{$purchase_order->id}}" class="btn btn-sm {{$purchase_order->statusBtnClass}}" href="/purchase_order/{{$purchase_order->id}}" title="Ver detalle de {{$purchase_order->orderName}}" target="_blank">{{$purchase_order->orderName}}</a></td>
			@if((Auth::user()->permissionViewSuppliers->state))
			<td id="2_{{$purchase_order->id}}"><a href="{{asset('supplier/'.$purchase_order->supplier->id)}}" target="_blank">{{$purchase_order->supplier->name}}</a></td>
			@endif
			<td id="3_{{$purchase_order->id}}" class="d-2-none">@can('seeUsersInformation')<a href="/user/{{$purchase_order->author->id}}">@endcan{{$purchase_order->author->name}}@can('seeUsersInformation')</a>@endcan</td>
			<td id="4_{{$purchase_order->id}}"><div id="p_s_btn_{{$purchase_order->id}}" onclick="upgradePurchase('{{$purchase_order->id}}', 0)" class="btn btn-sm {{$purchase_order->statusBtnClass}}" data-toggle="popover" data-placement="left" data-trigger="hover" data-content="Cambiar a {{$purchase_order->nextStatusName}}">{{$purchase_order->statusName}}</div><img class="f-right table_icon ml-2 hide" id="ld_{{$purchase_order->id}}" src="{{asset('/images/loading.gif')}}" /></td>
			@if((Auth::user()->permissionViewPurchase_OrderPrices->state))
			<td id="5_{{$purchase_order->id}}" class="d-3-none">@if(($purchase_order->quotedValue>0) AND ($purchase_order->status!=0)){{$purchase_order->quotedValue}} USD @else No cotizado @endif </td>
			@endif
			@if(Auth::user()->permissionViewPurchase_Orders->state AND Auth::user()->permissionViewPurchase_OrderPrices->state)
				@if(file_exists(storage_path('app').'/files/purchaseOrders/'.$purchase_order->id.'.pdf'))
			<td id="6_{{$purchase_order->id}}" class="text-center d-4-none"><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="Ver {{$purchase_order->orderName}}" title="Ver {{$purchase_order->orderName}}" onclick="goToPurchaseOrder('{{$purchase_order->id}}')" /></td>
				@else
			<td id="6_{{$purchase_order->id}}" class="text-center d-4-none"><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="{{$purchase_order->orderName}} no disponible" title="{{$purchase_order->orderName}} no disponible" /></td>
				@endif
			@endif
			@if((Auth::user()->permissionCreatePurchase_order->state))
				<td id="7_{{$purchase_order->id}}" class="d-4-none"><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editPurchase_order({{$purchase_order->id}})" /></td>
			@endif
			@if((Auth::user()->permissionDeletePurchase_order->state))
				@if($purchase_order->status!=6)
			<td id="8_{{$purchase_order->id}}" class="text-center d-4-none" class="text-center d-4-none"><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="upgradePurchase('{{$purchase_order->id}}', 6)" /></td>
				@else
			<td id="8_{{$purchase_order->id}}" class="text-center d-4-none"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeletePurchase_order(this)" /></td>
				@endif
			@endif
		</tr>
	@endforeach
		</tbody>
	</table>
	{{$purchase_orders->links()}}
@endsection
