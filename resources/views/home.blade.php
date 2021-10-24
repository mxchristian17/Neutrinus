@extends('layouts.app')
@section('pageTitle'){{"Panel -"}}@endsection
@section('scripts')
  @if(Auth::user()->permissionViewPurchase_Orders->state)
	<script type="application/javascript" src="{{ asset('js/Purchase.js')}}"></script>
	<script type="text/javascript">
		var orders = { @foreach ($purchases as $purchase){{$purchase->id}}:{{$purchase->order_number}} @if(!$loop->last), @endif @endforeach };
		var orders_status = { @foreach ($purchases as $purchase){{$purchase->id}}:{{$purchase->status}} @if(!$loop->last), @endif @endforeach }
	</script>
  @endif
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-left mb-2">
      @if(Auth::user()->permissionViewProjects->state)
        <div class="col-md-6 mb-2">
            <div class="card h-100 home-card">
                <div class="card-header bg-dark text-light">Proyectos recientes</div>
                <div class="card-body overflow-auto">
                    @if(!count($projects))
                    No se registran proyectos recientes
                    @endif
                    @foreach($projects as $project)
                    @if($loop->first)<table class="table table-striped table-hover"> @endif
                      @if(!$project->projectData->noExistente)
                      <tr>
                        <td><a href="{{asset('project/'.$project->projectData->id)}}">{{$project->projectData->name}}</a></td>
                      </tr>
                      @endif
                    @if($loop->last)
                  </table>
                    @endif
                    @endforeach
                </div>
                <div class="card-footer text-center">
                  <a href="{{asset('projects')}}">Ver todos los proyectos</a>
                </div>
            </div>
        </div>
        @endif
        @if(Auth::user()->permissionViewPurchase_Orders->state)
        <div class="col-md-6 mb-2">
          <div class="card h-100 home-card">
            <div class="card-header bg-dark text-light">Compras</div>
            <div class="card-body overflow-auto">
              @if(!count($purchases))
              No se registran procesos de compra activos
              @endif
              @foreach($purchases as $purchase)
              @if($loop->first)<table class="table table-striped table-hover"> @endif
                <tr class="row-purchase-status-{{$purchase->status}}">
                  <td id="1_{{$purchase->id}}"><a id="p_btn_{{$purchase->id}}" class="btn btn-sm {{$purchase->statusBtnClass}}" href="/purchase_order/{{$purchase->id}}" title="Ver detalle de {{$purchase->orderName}}" target="_blank">{{$purchase->orderName}}</a></td>
                  @if((Auth::user()->permissionViewSuppliers->state))
                  <td id="2_{{$purchase->id}}"><a href="{{asset('supplier/'.$purchase->supplier->id)}}" target="_blank">{{$purchase->supplier->name}}</a></td>
                  @endif
                  <td id="4_{{$purchase->id}}"><div id="p_s_btn_{{$purchase->id}}" onclick="upgradePurchase('{{$purchase->id}}', 0)" class="btn btn-sm {{$purchase->statusBtnClass}}" data-toggle="popover" data-placement="left" data-trigger="hover" data-content="Cambiar a {{$purchase->nextStatusName}}">{{$purchase->statusName}}</div><img class="f-right table_icon ml-2 hide" id="ld_{{$purchase->id}}" src="{{asset('/images/loading.gif')}}" /></td>
                  @if(Auth::user()->permissionViewPurchase_Orders->state AND Auth::user()->permissionViewPurchase_OrderPrices->state)
                  @if(file_exists(storage_path('app').'/files/purchaseOrders/'.$purchase->id.'.pdf'))
                  <td id="6_{{$purchase->id}}" class="text-center d-4-none"><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="Ver {{$purchase->orderName}}" title="Ver {{$purchase->orderName}}" onclick="goToPurchaseOrder('{{$purchase->id}}')" /></td>
                  @else
                  <td id="6_{{$purchase->id}}" class="text-center d-4-none"><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="{{$purchase->orderName}} no disponible" title="{{$purchase->orderName}} no disponible" /></td>
                  @endif
                  @endif
                </tr>
                @if($loop->last)
              </table>
              @endif
              @endforeach
            </div>
            <div class="card-footer text-center">
              <a href="{{asset('purchase_orders')}}">Ver todas las compras</a>
            </div>
          </div>
        </div>
        @endif
        @if(count($underChargeUsers) > 0)
        <div class="col-md-6 mb-2">
            <div class="card h-100 home-card">
                <div class="card-header bg-dark text-light">Personal a cargo</div>
                <div class="card-body overflow-auto">
                    @if(!count($underChargeUsers))
                    No tienes personal a cargo aún...
                    @endif
                    @foreach($underChargeUsers as $userUnderCharge)
                    @if($loop->first)<table class="table table-striped"> @endif
                      <tr>
                        <td><a href="{{asset('user/'.$userUnderCharge->user_under_charge->id)}}" class="avatar rounded-circle m-1 float-left"><img src="{{ route('avatarImg', $userUnderCharge->user_under_charge->id.'.jpg') }}" alt="Avatar" class="img img-responsive full-width"></a><a href="{{asset('user/'.$userUnderCharge->user_under_charge->id)}}" class="pl-1" style="line-height:40px;">{{$userUnderCharge->user_under_charge->name}}</a></td>
                      </tr>
                    @if($loop->last)
                  </table>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @if(Auth::user()->permissionViewSales->state AND Auth::user()->permissionViewClients->state)
        <div class="col-md-6 mb-2">
          <div class="card h-100 home-card">
            <div class="card-header bg-dark text-light">Ventas</div>
            <div class="card-body overflow-auto">
              @if(!count($sales))
              No se registran procesos de venta activos
              @endif
              @foreach($sales as $sale)
              @if($loop->first)<table class="table table-striped table-hover">
                  <thead class="thead-secondary">
                    <tr>
                      <th>Venta</th>
                      <th>Cliente</th>
                      <th>Estado</th>
                      <th>Fecha requerida</th>
                    </tr>
                  </thead>@endif
                <tr class="background-color-row-state-{{$sale->state->id}}">
                  <td><a href="{{asset('sale/'.$sale->id)}}">{{$sale->saleName}}</a></td>
                  <td><a href="{{asset('client/'.$sale->client->id)}}">{{$sale->client->name}}</a></td>
                  <td><span class="btn-sm {{$sale->statusBtnClass}}">{{$sale->statusName}}</span></td>
                  <td>{{\Carbon\Carbon::parse($sale->requested_delivery_date)->format('d/m/Y')}}</td>
                </tr>
                @if($loop->last)
              </table>
              @endif
              @endforeach
            </div>
            <div class="card-footer text-center">
              <a href="{{asset('sales')}}">Ver todas las ventas</a>
            </div>
          </div>
        </div>
        @endif
    </div>
</div>
@endsection
