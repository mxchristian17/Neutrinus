@extends('layouts.app')
@section('pageTitle')Cargar nuevo proceso de venta - @endsection
@section('scripts')
<script type="application/javascript" src="{{ asset('js/Sale.js')}}"></script>
<script type="text/javascript">
var saleElementsUrl = "{{ route('autocompleteelement.salefetch') }}";
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
  <h1>Carga de nuevo proceso de venta</h1>
  {!! Form::model($sale, [
      'method' => 'POST',
      'route' => ['updatesale', $sale->id]
  ]) !!}
  <div class="input-group input-group-sm mt-1">
    <div class="input-group-prepend">
      <span class="input-group-text">Cliente</span>
    </div>
    {!! Form::select('client_id', $client_id, old('client_id') ?? $sale->client->id,['class'=>'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1">
    <div class="input-group-prepend">
      <span class="input-group-text">Estado</span>
    </div>
    {!! Form::select('status', $status, old('status') ?? $sale->status,array_merge(['class'=>'form-control'], ['onchange' => 'statusChange(this)'], ['id' => 'status_selector'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1 bill_number">
    <div class="input-group-prepend">
      <span class="input-group-text">Número de factura</span>
    </div>
    {!! Form::text('bill_number', old('bill_number') ?? $sale->bill_number, array_merge(['class' => 'form-control'], ['placeholder' => 'Número de factura...'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1 order_number">
    <div class="input-group-prepend">
      <span class="input-group-text">Código de órden de compra</span>
    </div>
    {!! Form::text('order_number', old('order_number') ?? $sale->order_number, array_merge(['class' => 'form-control'], ['placeholder' => 'Código de órden de compra...'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1 quoted_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha de cotizacion</span>
    </div>
    {!! Form::input('datetime-local', 'quoted_date', old('quoted_date') ?? Carbon\Carbon::parse($sale->quoted_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 purchase_order_reception_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha de recepción de órden de compras</span>
    </div>
    {!! Form::input('datetime-local', 'purchase_order_reception_date', old('purchase_order_reception_date') ?? Carbon\Carbon::parse($sale->purchase_order_reception_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 requested_delivery_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha de entrega solicitada</span>
    </div>
    {!! Form::input('datetime-local', 'requested_delivery_date', old('requested_delivery_date') ?? Carbon\Carbon::parse($sale->requested_delivery_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 scheduled_delivery_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha de entrega programada</span>
    </div>
    {!! Form::input('datetime-local', 'scheduled_delivery_date', old('scheduled_delivery_date') ?? Carbon\Carbon::parse($sale->scheduled_delivery_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 ready_to_deliver_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha en que estuvo listo para entregar</span>
    </div>
    {!! Form::input('datetime-local', 'ready_to_deliver_date', old('ready_to_deliver_date') ?? Carbon\Carbon::parse($sale->ready_to_deliver_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 delivered_date">
    <div class="input-group-prepend">
      <span class="input-group-text">Fecha de entrega ejecutada</span>
    </div>
    {!! Form::input('datetime-local', 'delivered_date', old('delivered_date') ?? Carbon\Carbon::parse($sale->delivered_date)->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 currencies">
    <div class="input-group-prepend">
      <span class="input-group-text">Moneda de facturación</span>
    </div>
    {!! Form::select('currency_id', $currencies, old('currency_id') ?? $sale->currency_id,['class'=>'form-control']) !!}
  </div>
  <div class="input-group input-group-sm mt-1 retentions">
    <div class="input-group-prepend">
      <span class="input-group-text">Monto de retenciones</span>
    </div>
    {!! Form::number('retentions', old('retentions') ?? $sale->retentions, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1 perceptions">
    <div class="input-group-prepend">
      <span class="input-group-text">Monto de percepciones</span>
    </div>
    {!! Form::number('perceptions', old('perceptions') ?? $sale->perceptions, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1">
    {!! Form::textarea('observations', old('observations') ?? $sale->observations, array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones generales de la venta...'], ['style' => 'height:100px;'])) !!}
  </div>
  <div class="input-group input-group-sm mt-1">
    <div class="input-group-prepend">
      <span class="input-group-text">Estado del proceso de venta</span>
    </div>
    {!! Form::select('state_id', $states, old('state_id') ?? $sale->state_id,['class'=>'form-control']) !!}
  </div>
  @foreach($sale->items as $item)
    @if($item->project_id)
  <div class="row mt-3 pt-2 border-top border-danger">
    <div class="col-12 mt-1">
      <h5>Proyecto</h5>
    </div>
    <div class="col-sm-6">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Proyecto</span>
        </div>
        {!! Form::select('project_project_id[]', $project_id, $item->project_id,['class'=>'form-control']) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Cantidad</span>
        </div>
        {!! Form::number('project_quantity[]', $item->quantity, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Número de serie (opcional)</span>
        </div>
        {!! Form::number('project_serial_number[]', $item->serial_number, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group mt-1">
        {!! Form::textarea('project_observations[]', $item->observations, array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
      </div>
    </div>
    <div class="col-sm-6 mt-1">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Valor cotizado</span>
        </div>
        {!! Form::number('project_quotedValue[]', $item->quotedValue, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% descuento item</span>
        </div>
        {!! Form::number('project_discount[]', $item->discount, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'], ['max' => '100'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">%IVA</span>
        </div>
        {!! Form::number('project_iva_tax_percentaje[]', $item->iva_tax_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% otros impuestos</span>
        </div>
        {!! Form::number('project_other_taxes_percentaje[]', $item->other_taxes_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover proyecto</div>
    </div>
  </div>
    @endif

    @if($item->subset_id)
    <div class="row mt-3 pt-2 border-top border-danger">
      <div class="col-12 mt-1">
        <h5>Subconjunto</h5>
      </div>
      <div class="col-sm-6">
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Proyecto</span>
          </div>
          {!! Form::select('subset_project_id[]', $project_id, old('subset_project_id') ?? $item->subset->project_id, array_merge(['class'=>'form-control preloaded_projects'], ['subset' => $item->subset_id], ['onchange' => 'saleCompleteSubsetsSelector(this)'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1 subset-container">
          <div class="input-group-prepend">
            <span class="input-group-text">Subconjunto de destino</span>
          </div>
          <select name="subset_subset_id[]" class="form-control subset">
            <option></option>
          </select>
        </div>
        <div class="row py-1 hide subset_ld">
          <div class="col w-100 align-self-center text-center">
            <img class="f-right table_icon mx-2 align-middle" src="{{asset('/images/loading.gif')}}" /><span class="align-middle">Cargando...</span>
          </div>
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Cantidad</span>
          </div>
          {!! Form::number('subset_quantity[]', old('subset_quantity') ?? $item->quantity, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Número de serie (opcional)</span>
          </div>
          {!! Form::number('subset_serial_number[]', old('subset_serial_number') ?? $item->serial_number, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
        </div>
        <div class="input-group mt-1">
          {!! Form::textarea('subset_observations[]', old('subset_observations') ?? $item->observations, array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
        </div>
      </div>
      <div class="col-sm-6 mt-1">
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Valor cotizado</span>
          </div>
          {!! Form::number('subset_quotedValue[]', old('subset_quotedValue') ?? $item->quotedValue, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">% descuento item</span>
          </div>
          {!! Form::number('subset_discount[]', old('subset_discount') ?? $item->discount, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">%IVA</span>
          </div>
          {!! Form::number('subset_iva_tax_percentaje[]', old('subset_iva_tax_percentaje') ?? $item->iva_tax_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">% otros impuestos</span>
          </div>
          {!! Form::number('subset_other_taxes_percentaje[]', old('subset_other_taxes_percentaje') ?? $item->other_taxes_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover subconjunto</div>
      </div>
    </div>
    @endif

    @if($item->element_id)
    <div class="row mt-3 pt-2 border-top border-danger">
      <div class="col-12 mt-1">
        <h5>Elemento</h5>
      </div>
      <div class="col-sm-6">
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Elemento</span>
          </div>
          {!! Form::text('element_element_id[]', old('element_element_id') ?? $item->element_id, array_merge(['class' => 'form-control sale_element_input hide'], ['placeholder' => 'Elemento...'], ['onkeyup' => 'sale_element_keyup(this, event)'], ['autocomplete' => 'off'])) !!}
          <div class="autocomplete-items dropdown-menu saleElementIdList"></div>
          <div class="input-group-append sale_element_input_selected">
            <span class="input-group-text bg-muted">{{$item->element->name}} ({{$item->element->nro}}-{{$item->element->add}})</span>
          </div>
          <div class="input-group-append sale_element_input_cancel_button">
            <button class="btn btn-outline-danger" type="button" onclick="cancelElementSelection(this)">X</button>
          </div>
        </div>
        <div class="row py-1 hide element_ld">
          <div class="col w-100 align-self-center text-center">
            <img class="f-right table_icon mx-2 align-middle" src="{{asset('/images/loading.gif')}}" /><span class="align-middle">Cargando...</span>
          </div>
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Cantidad</span>
          </div>
          {!! Form::number('element_quantity[]', old('element_quantity') ?? $item->quantity, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Número de serie (opcional)</span>
          </div>
          {!! Form::number('element_serial_number[]', old('element_serial_number') ?? $item->serial_number, array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
        </div>
        <div class="input-group mt-1">
          {!! Form::textarea('element_observations[]', old('element_observations') ?? $item->observations, array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
        </div>
      </div>
      <div class="col-sm-6 mt-1">
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">Valor cotizado</span>
          </div>
          {!! Form::number('element_quotedValue[]', old('element_quotedValue') ?? $item->quotedValue, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">% descuento item</span>
          </div>
          {!! Form::number('element_discount[]', old('element_discount') ?? $item->discount, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">%IVA</span>
          </div>
          {!! Form::number('element_iva_tax_percentaje[]', old('element_iva_tax_percentaje') ?? $item->iva_tax_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="input-group input-group-sm mt-1">
          <div class="input-group-prepend">
            <span class="input-group-text">% otros impuestos</span>
          </div>
          {!! Form::number('element_other_taxes_percentaje[]', old('element_other_taxes_percentaje') ?? $item->other_taxes_percentaje, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
        </div>
        <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover elemento</div>
      </div>
    </div>
    @endif
  @endforeach
  <div class="row d-none project mt-3 pt-2 border-top border-danger">
    <div class="col-12 mt-1">
      <h5>Proyecto</h5>
    </div>
    <div class="col-sm-6">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Proyecto</span>
        </div>
        {!! Form::select('project_project_id[]', $project_id, old('project_project_id'),['class'=>'form-control']) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Cantidad</span>
        </div>
        {!! Form::number('project_quantity[]', old('project_quantity'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Número de serie (opcional)</span>
        </div>
        {!! Form::number('project_serial_number[]', old('project_serial_number'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group mt-1">
        {!! Form::textarea('project_observations[]', old('project_observations'), array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
      </div>
    </div>
    <div class="col-sm-6 mt-1">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Valor cotizado</span>
        </div>
        {!! Form::number('project_quotedValue[]', old('project_quotedValue'), array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% descuento item</span>
        </div>
        {!! Form::number('project_discount[]', old('project_discount') ?? 0, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'], ['max' => '100'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">%IVA</span>
        </div>
        {!! Form::number('project_iva_tax_percentaje[]', old('project_iva_tax_percentaje') ?? 21, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% otros impuestos</span>
        </div>
        {!! Form::number('project_other_taxes_percentaje[]', old('project_other_taxes_percentaje') ?? 4, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover proyecto</div>
    </div>
  </div>

  <div class="row d-none subset-block mt-3 pt-2 border-top border-danger">
    <div class="col-12 mt-1">
      <h5>Subconjunto</h5>
    </div>
    <div class="col-sm-6">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Proyecto</span>
        </div>
        {!! Form::select('subset_project_id[]', $project_id, old('subset_project_id'), array_merge(['class'=>'form-control'], ['onchange' => 'saleCompleteSubsetsSelector(this)'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1 subset-container">
        <div class="input-group-prepend">
          <span class="input-group-text">Subconjunto de destino</span>
        </div>
        <select name="subset_subset_id[]" class="form-control subset">
          <option></option>
        </select>
      </div>
      <div class="row py-1 hide subset_ld">
        <div class="col w-100 align-self-center text-center">
          <img class="f-right table_icon mx-2 align-middle" src="{{asset('/images/loading.gif')}}" /><span class="align-middle">Cargando...</span>
        </div>
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Cantidad</span>
        </div>
        {!! Form::number('subset_quantity[]', old('subset_quantity'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Número de serie (opcional)</span>
        </div>
        {!! Form::number('subset_serial_number[]', old('subset_serial_number'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group mt-1">
        {!! Form::textarea('subset_observations[]', old('subset_observations'), array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
      </div>
    </div>
    <div class="col-sm-6 mt-1">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Valor cotizado</span>
        </div>
        {!! Form::number('subset_quotedValue[]', old('subset_quotedValue'), array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% descuento item</span>
        </div>
        {!! Form::number('subset_discount[]', old('subset_discount') ?? 0, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">%IVA</span>
        </div>
        {!! Form::number('subset_iva_tax_percentaje[]', old('subset_iva_tax_percentaje') ?? 21, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% otros impuestos</span>
        </div>
        {!! Form::number('subset_other_taxes_percentaje[]', old('subset_other_taxes_percentaje') ?? 4, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover subconjunto</div>
    </div>
  </div>

  <div class="row d-none element-block mt-3 pt-2 border-top border-danger">
    <div class="col-12 mt-1">
      <h5>Elemento</h5>
    </div>
    <div class="col-sm-6">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Elemento</span>
        </div>
        {!! Form::text('element_element_id[]', old('element_element_id'), array_merge(['class' => 'form-control sale_element_input'], ['placeholder' => 'Elemento...'], ['onkeyup' => 'sale_element_keyup(this, event)'], ['autocomplete' => 'off'])) !!}
        <div class="autocomplete-items dropdown-menu saleElementIdList"></div>
        <div class="input-group-append hide sale_element_input_selected">
          <span class="input-group-text bg-muted"></span>
        </div>
        <div class="input-group-append hide sale_element_input_cancel_button">
          <button class="btn btn-outline-danger" type="button" onclick="cancelElementSelection(this)">X</button>
        </div>
      </div>
      <div class="row py-1 hide element_ld">
        <div class="col w-100 align-self-center text-center">
          <img class="f-right table_icon mx-2 align-middle" src="{{asset('/images/loading.gif')}}" /><span class="align-middle">Cargando...</span>
        </div>
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Cantidad</span>
        </div>
        {!! Form::number('element_quantity[]', old('element_quantity'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Número de serie (opcional)</span>
        </div>
        {!! Form::number('element_serial_number[]', old('element_serial_number'), array_merge(['class' => 'form-control'], ['step' => '1'], ['min' => '0'])) !!}
      </div>
      <div class="input-group mt-1">
        {!! Form::textarea('element_observations[]', old('element_observations'), array_merge(['class' => 'form-control'], ['placeholder' => 'Observaciones particulares del item...'], ['style' => 'height:100px;'])) !!}
      </div>
    </div>
    <div class="col-sm-6 mt-1">
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">Valor cotizado</span>
        </div>
        {!! Form::number('element_quotedValue[]', old('element_quotedValue'), array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% descuento item</span>
        </div>
        {!! Form::number('element_discount[]', old('element_discount') ?? 0, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">%IVA</span>
        </div>
        {!! Form::number('element_iva_tax_percentaje[]', old('element_iva_tax_percentaje') ?? 21, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
          <span class="input-group-text">% otros impuestos</span>
        </div>
        {!! Form::number('element_other_taxes_percentaje[]', old('element_other_taxes_percentaje') ?? 4, array_merge(['class' => 'form-control'], ['step' => '0.01'], ['min' => '0'])) !!}
      </div>
      <div class="btn btn-danger float-right mt-1" onclick="removeItem(this);">Remover elemento</div>
    </div>
  </div>

  <hr>
  <div id="sales"></div>
  <div class="row">
  <div class="col-md-4 mt-2  text-center" id="add_project">
    <div class="btn btn-sm btn-success">Añadir proyecto</div>
  </div>
  <div class="col-md-4 mt-2  text-center" id="add_subset">
    <div class="btn btn-sm btn-success">Añadir subconjunto</div>
  </div>
  <div class="col-md-4 mt-2 text-center">
    <div class="btn btn-sm btn-success" id="add_element">Añadir elemento</div>
  </div>
  </div>
  <br />
  {!! Form::submit('Guardar', ['class' => 'form-control btn btn-primary']) !!}
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
</div>
@endsection
