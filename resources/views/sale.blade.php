@extends('layouts.app')
@section('pageTitle')Proceso de venta {{$sale->saleName}} - @endsection
@section('scripts')
<script type="application/javascript" src="{{ asset('js/Sale.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
  $('.project').on('click', function(event){
    toggleElement = $('.' + $(this).closest('tr').attr('id'));
    toggleElement.each(function( index ) {
      toggleSubElement = $('.' + $(this).closest('tr').attr('id'));
      toggleSubElement.hide();
    });
    toggleElement.toggle();
    if(toggleElement.is(':Visible'))
    {
      $([document.documentElement, document.body]).animate({
          scrollTop: ($(this).offset().top - 100)
      }, 500);
    }
  });
  $('.subset').on('click', function(event){
    var toggleElement = $('.' + $(this).closest('tr').attr('id'));
    toggleElement.toggle();
    if(toggleElement.is(':Visible'))
    {
      $([document.documentElement, document.body]).animate({
          scrollTop: ($(this).offset().top - 100)
      }, 500);
    }
  })
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
  <div class="row px-2 d-print-none">
    <div class="px-1 py-0 mb-2">Administración ></div>
    <div class="px-1 py-0 mb-2"><a href="{{asset('sales')}}">Ventas</a></div>
  </div>
  <h1 class="d-inline">{{$sale->saleName}}</h1>
  @if((Auth::user()->permissionCreateSale->state))
  <img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top d-print-none" onclick="editSale({{$sale->id}})" />
  @endif
<table class=" mt-3 table table-sm">
  <tr>
    <td>Proceso de venta {{$sale->saleName}}</td>
    <td>Estado: <span class="btn-sm {{$sale->statusBtnClass}}">{{$sale->statusName}}</span></td>
  </tr>
  @if((Auth::user()->permissionViewClients->state))
  <tr>
    <td><span class="d-2-none">Cliente: </span><a href="{{asset('client/'.$sale->client->id)}}" target="_blank">{{$sale->client->name}}</a></td>
    <td><span class="d-2-none">Mail: </span>{{$sale->client->email}}</td>
  </tr>
  <tr>
    <td><span class="d-2-none">Dirección: </span>{{$sale->client->completeAddress}}</td>
    <td><span class="d-2-none">Teléfono: </span>{{$sale->client->phone_number}}</td>
  </tr>
  @endif
  <tr>
    <td>Fecha de generación: {{\Carbon\Carbon::parse($sale->created_at)->format('d/m/Y')}} <small class="d-2-none d-print-none">(Por @can('seeUsersInformation')<a href="/user/{{$sale->author->id}}" target="_blank">@endcan{{$sale->author->name}}@can('seeUsersInformation')</a>@endcan {{\Carbon\Carbon::parse($sale->created_at)->diffForHumans()}})</small></td>
    @if($sale->status == 1)<td>Solicitud de cotización: {{\Carbon\Carbon::parse($sale->quote_request_date)->format('d/m/Y')}} <small class="d-2-none d-print-none">{{\Carbon\Carbon::parse($sale->quote_request_date)->diffForHumans()}}</small></td>@endif
    @if($sale->status > 1 AND $sale->status < 8)<td>Fecha de cotización: {{\Carbon\Carbon::parse($sale->quote_date)->format('d/m/Y')}} <small class="d-2-none d-print-none">{{\Carbon\Carbon::parse($sale->quote_date)->diffForHumans()}}</small></td>@endif
    @if($sale->status == 8)<td></td>@endif
  </tr>
  <tr>
    <td>Entrega solicitada: {{\Carbon\Carbon::parse($sale->requested_delivery_date)->format('d/m/Y')}} <small class="d-2-none d-print-none">{{\Carbon\Carbon::parse($sale->requested_delivery_date)->diffForHumans()}}</small></td>
    @if($sale->status < 6)<td>Entrega programada: {{\Carbon\Carbon::parse($sale->scheduled_delivery_date)->format('d/m/Y')}} <small class="d-2-none d-print-none">{{\Carbon\Carbon::parse($sale->scheduled_delivery_date)->diffForHumans()}}</small></td>@endif
    @if($sale->status >= 6 AND $sale->status < 8)<td>Entrega ejecutada: {{\Carbon\Carbon::parse($sale->delivered_date)->format('d/m/Y')}} <small class="d-2-none d-print-none">{{\Carbon\Carbon::parse($sale->delivered_date)->diffForHumans()}}</small></td>@endif
    @if($sale->status == 8)<td></td>@endif
  </tr>
  @if($sale->status >=2)
  <tr>
    <td>@if($sale->status >=3)OC: {{$sale->order_number}} <small class="d-2-none d-print-none">Recibida {{\Carbon\Carbon::parse($sale->purchase_order_reception_date)->diffForHumans()}}</small>@endif</td>
    <td>@if($sale->status >=2)<b>Moneda:</b> ({{$sale->currency->prefix}}) {{$sale->currency->name}}@endif</td>
  </tr>
  @endif
  @if($sale->status >=4)
  <tr>
    <td>N° factura: {{$sale->bill_number}}</td>
    <td></td>
  </tr>
  @endif
  @if($sale->observations)
  <tr>
    <td colspan="2">{{$sale->observations}}</span></td>
  </tr>
  @endif

</table>

<h4>Detalle</h4>
<table class="table table-hover">
  <thead class="text-center border border-muted">
    <th class="text-left pl-3">Item</th>
    <th>Cantidad</th>
    <th class="d-2-none">Observaciones</th>
    <th class="d-4-none">Precio<span class="d-3-none"> unitario</span></th>
    <th class="d-4-none">Descuento<span class="d-3-none"> unitario</span></th>
    <th class="d-4-none">Impuesto<span class="d-3-none"> unitario</span></th>
    <th><span class="d-3-none">Precio </span>total</th>
    <th></th>
    <th></th>
  </thead>
  <tbody class="border border-muted">
    @foreach($sale->items as $item)

      @if($item->project_id)
    <tr id="{{$item->id}}_{{$item->project->id}}" class="cursor-pointer text-center m-0 p-0 project {{$item->id}}">
      <td class="text-left"><span class="pl-2"><span class="text-primary pr-1 pr-1 d-print-none">►</span>{{$item->project->name}}</span></td>
      <td>{{$item->quantity}}</td>
      <td class="d-2-none">{{$item->observations}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->quotedValue}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->discountVal}} <span class="d-3-none">({{$item->discount}}%)</span></td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->taxVal}}</td>
      <td><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->totalVal}}</td>
      <td></td>
      <td></td>
    </tr>
        @foreach($item->project->subsets as $subset)
        @if($subset->state_id == 1 or ($subset->state_id == 3 AND Auth()->user()->permissionViewHiddenElements))
    <tr id="{{$item->id}}_{{$item->project->id}}_{{$subset->id}}" class="hide cursor-pointer text-center m-0 p-0 subset {{$item->id}}_{{$item->project->id}} pr-1 d-print-none">
      <td class="text-left pl-3"><span class="pl-3"><span class="text-success pr-1">►</span>{{$subset->name}}</span></td>
      <td></td>
      <td class="d-2-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
          @foreach($subset->projectelement as $projectelement)
          @if($projectelement->specific_state_id == 1 or ($projectelement->specific_state_id == 3 AND Auth()->user()->permissionViewHiddenElements))
    <tr class="hide cursor-pointer text-center m-0 p-0 element {{$item->id}}_{{$item->project->id}}_{{$subset->id}} pr-1 d-print-none">
      <td class="text-left pl-4"><span class="pl-4"><span class="text-danger pr-1">●</span>@if((Auth::user()->permissionViewElements->state)) <a target="_blank" href="{{asset('/projectelement/'.$projectelement->id)}}"> @endif {{$projectelement->element->name}}@if((Auth::user()->permissionViewElements->state)) </a> @endif </span></td>
      <td>{{$projectelement->quantity*$item->quantity}}<small class="text-secondary">({{$item->quantity}}x{{$projectelement->quantity}})</small></td>
      <td class="d-2-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
          @endif
          @endforeach
        @endif
        @endforeach

      @endif

      @if($item->subset_id)
      @if($item->subset->state_id == 1 or ($item->subset->state_id == 3 AND Auth()->user()->permissionViewHiddenElements))
    <tr id="{{$item->id}}_{{$item->subset->id}}" class="cursor-pointer text-center m-0 p-0 subset">
      <td class="text-left"><span class="pl-2"><span class="text-success pr-1 pr-1 d-print-none">►</span>{{$item->subset->name}}</span></td>
      <td>{{$item->quantity}}</td>
      <td class="d-2-none">{{$item->observations}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->quotedValue}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->discountVal}} <span class="d-3-none">({{$item->discount}}%)</span></td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->taxVal}}</td>
      <td><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->totalVal}}</td>
      <td></td>
      <td></td>
    </tr>
          @foreach($item->subset->projectelement as $projectelement)
          @if($projectelement->specific_state_id == 1 or ($projectelement->specific_state_id == 3 AND Auth()->user()->permissionViewHiddenElements))
    <tr class="hide cursor-pointer text-center m-0 p-0 element {{$item->id}}_{{$item->subset->id}} pr-1 d-print-none">
      <td class="text-left pl-3"><span class="pl-3"><span class="text-danger pr-1">●</span>@if((Auth::user()->permissionViewElements->state)) <a target="_blank" href="{{asset('/projectelement/'.$projectelement->id)}}"> @endif {{$projectelement->element->name}}@if((Auth::user()->permissionViewElements->state)) </a> @endif </span></td>
      <td>{{$projectelement->quantity*$item->quantity}}<small class="text-secondary">({{$item->quantity}}x{{$projectelement->quantity}})</small></td>
      <td class="d-2-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
          @endif
          @endforeach
      @endif
      @endif

      @if($item->element_id)
    <tr class="cursor-pointer text-center m-0 p-0 element">
      <td class="text-left"><span class="pl-2"><span class="text-danger pr-1 d-print-none">●</span>@if((Auth::user()->permissionViewElements->state)) <a target="_blank" href="{{asset('/element/'.$item->element->id)}}"> @endif {{$item->element->name}}@if((Auth::user()->permissionViewElements->state)) </a> @endif </span></td>
      <td>{{$item->quantity}}</td>
      <td class="d-2-none">{{$item->observations}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->quotedValue}}</td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->discountVal}} <span class="d-3-none">({{$item->discount}}%)</span></td>
      <td class="d-4-none"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->taxVal}}</td>
      <td><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$item->totalVal}}</td>
      <td></td>
      <td></td>
    </tr>
      @endif

    @endforeach
  </tbody>
  <tbody class=" table-sm text-primary border-0 text-center">
    <tr class="m-0 p-0 bg-danger d-print-none">
      <td class="border-top border-danger"></td>
      <td class="border-top border-danger"></td>
      <td class="border-top border-danger d-2-none"></td>
      <td class="border-top border-danger d-4-none"></td>
      <td class="border-top border-danger d-4-none"></td>
      <td class="border-top border-danger d-4-none"></td>
      <td class="border-top border-danger"></td>
      <td class="border-top border-danger"></td>
      <td class="border-top border-danger"></td>
    </tr>
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Subtotal:</td>
      <td><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->quotedValue}}</td>
      <td></td>
      <td></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @if($sale->discountVal)
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Descuento:</td>
      <td></td>
      <td class="text-success"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->discountVal}}</td>
      <td></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @endif
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">IVA:</td>
      <td></td>
      <td></td>
      <td class="text-danger"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->ivaTaxVal}}</td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Otros impuestos:</td>
      <td></td>
      <td></td>
      <td class="text-danger"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->otherTaxVal}}</td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @if($sale->perceptions)
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Percepciones:</td>
      <td></td>
      <td></td>
      <td class="text-danger"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->perceptions}}</td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @endif
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Total:</td>
      <td></td>
      <td></td>
      <td class="d-4-none"></td>
      <td><span class="d-3-none">{{$sale->currency->prefix}}</span> {{$sale->totalVal}}</td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @if($sale->retentions)
    <tr class="m-0 p-0">
      <td></td>
      <td class="d-2-none"></td>
      <td class="text-right">Retenciones:</td>
      <td></td>
      <td></td>
      <td class="d-4-none"></td>
      <td class="text-danger"><span class="d-3-none">{{$sale->currency->prefix}}</span> {{intval($sale->retentions)}}</td>
      <td class="d-4-none"></td>
      <td class="d-4-none"></td>
    </tr>
    @endif
  </tbody>
</table>

<table class=" mt-3 table table-sm">
  <tr>
    <td>Emisor órden de trabajo: @can('seeUsersInformation')<a href="/user/{{$sale->workOrderEmitter->id}}" target="_blank">@endcan{{$sale->workOrderEmitter->name}}@can('seeUsersInformation')</a>@endcan</td>
  </tr>
  @if($sale->work_order_observations)
  <tr>
    <td>{{$sale->work_order_observations}}</td>
  </tr>
  @endif
</table>


@endsection
