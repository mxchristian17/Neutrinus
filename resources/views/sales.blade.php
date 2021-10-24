@extends('layouts.app')
@section('pageTitle')Ventas - @endsection
@section('content')
<div class="container">
  @if (Route::has('login'))
  <div class="top-right links">
    @auth
      <h1>Ventas</h1>
    @else

    @endauth
  </div>
  @endif
@if(Auth::user()->permissionCreateSale->state)
<a class="btn btn-link mr-2 mb-1 p-1" href="/createsale">Crear nuevo proceso de venta</a>
@endif
<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
<table id="sales" class="table table-hover">
  <thead class="thead-dark">
    <tr>
      <th onclick="sortTable('sales', 0)">Nombre</th>
      <th onclick="sortTable('sales', 1)">@if((Auth::user()->permissionViewClients->state))Cliente @endif</th>
      <th onclick="sortTable('sales', 2)">Estado</th>
      <th onclick="sortTable('sales', 3)">Pedido</th>
      <th onclick="sortTable('sales', 4)">Solicitado</td>
      <th onclick="sortTable('sales', 5)">Entregado</th>
      <th onclick="sortTable('sales', 6)">Autor</th>
      @if((Auth::user()->permissionCreateSale->state))
      <th></th>
      @endif
      @if((Auth::user()->permissionDeleteSale->state))
      <th></th>
      @endif
    </tr>
  </thead>
  <tbody id="tbodySales">
    @foreach($sales as $sale)
    <tr class="background-color-row-state-{{$sale->state->id}}">
      <td><a href="{{asset('sale/'.$sale->id)}}">{{$sale->saleName}}</a></td>
      <td>@if((Auth::user()->permissionViewClients->state))<a href="{{asset('client/'.$sale->client->id)}}">{{$sale->client->name}}</a>@endif</td>
      <td><span class="btn-sm {{$sale->statusBtnClass}}">{{$sale->statusName}}</span></td>
      <td>@if($sale->status > 2 AND $sale->status < 8){{\Carbon\Carbon::parse($sale->purchase_order_reception_date)->format('d/m/Y')}}@endif </td>
      <td>{{\Carbon\Carbon::parse($sale->requested_delivery_date)->format('d/m/Y')}}</td>
      <td>@if($sale->status >= 6 AND $sale->status < 8){{\Carbon\Carbon::parse($sale->delivered_date)->format('d/m/Y')}}@endif </td>
      <td>@can('seeUsersInformation')<a href="/user/{{$sale->author->id}}">@endcan{{$sale->author->name}}@can('seeUsersInformation')</a>@endcan</td>
      @if((Auth::user()->permissionCreateSale->state))
      <td></td>
      @endif
      @if((Auth::user()->permissionDeleteSale->state))
      <td></td>
      @endif
    </tr>
    @endforeach
  </tbody>
</table>
</div>
@endsection
