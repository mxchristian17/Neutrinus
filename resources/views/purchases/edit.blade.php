@extends('layouts.app')
@section('pageTitle'){{ "Editar ". $purchase->orderType." ".$purchase->orderName." -" }} @endsection
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
		<h1>Editar {{$purchase->orderType}} {{$purchase->orderName}}</h1>
		{!! Form::model($purchase, [
		    'method' => 'POST',
		    'route' => ['updatepurchaseorder', $purchase->id]
		]) !!}
		@if($purchase->status < 3)
		<div class="form-group mt-3">
				{!! Form::Label('supplier_id', 'Proveedor', ['class' => 'control-label']) !!}
				{!! Form::select('supplier_id', $suppliers, $purchase->supplier_id, ['class' => 'form-control mb-3']) !!}
				@error('supplier_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		<div class="form-group">
		    {!! Form::label('observations', 'Observaciones', ['class' => 'control-label']) !!}
		    {!! Form::textarea('observations', old('observations'), array_merge(['class' => 'form-control'], ['rows' => '3'], ['placeholder' => 'Escriba aquí las observaciones generales...'])) !!}
				@error('observations')
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
		{!! Form::submit('Actualizar '.$purchase->orderType, ['class' => 'btn btn-primary']) !!}
		@elseif($purchase->status != 6)
		<p class="text-danger">Esta órden no puede ser editada porque ya ha sido emitida. Si desea editarla, deberá revertirla previamente.</p>
		@else
		<p class="text-danger">Esta órden ha sido anulada. Para editarla, debe ser habilitada previamente.</p>
		@endif
		@if($purchase->status > 3 AND $purchase->status != 6)
		<div class="form-group">
		    {!! Form::label('order_receipt_observations', 'Observaciones de la recepción del pedido', ['class' => 'control-label']) !!}
		    {!! Form::textarea('order_receipt_observations', old('order_receipt_observations'), array_merge(['class' => 'form-control'], ['rows' => '3'], ['placeholder' => 'Escriba aquí las observaciones correspondientes a la recepción del pedido...'])) !!}
				@error('observations')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
		</div>
		@endif
		<a class="btn btn-link" href="{{ url()->previous() }}">Volver</a>
		{!! Form::close() !!}
@endsection
