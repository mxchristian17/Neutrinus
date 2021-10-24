@extends('layouts.app')
@section('pageTitle'){{ "Editar proveedor ".$supplier->name." -" }} @endsection
@section('scripts')
	<script type="application/javascript" src="{{asset('js/Supplier.js')}}"></script>
@endsection
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
		<h1>Editar a {{$supplier->name}}</h1>
		{!! Form::model($supplier, [
		    'method' => 'POST',
		    'route' => ['updatesupplier', $supplier->id]
		]) !!}
		{!! Form::text('name', old('name'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Nombre del Proveedor...'])) !!}
		{!! Form::tel('phone_number', old('phone_number'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Teléfono...'])) !!}
		{!! Form::text('email', null, array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Email...'])) !!}
		{!! Form::textarea('description', old('description'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Descripción...'], ['style' => 'height:100px;'])) !!}
		<div class="row">
			<div class="col-sm-6 mt-1">
				{!! Form::text('country', old('country'), array_merge(['class' => 'form-control'], ['placeholder' => 'País...'])) !!}
			</div>
			<div class="col-sm-6 mt-1">
				{!! Form::text('province', old('province'), array_merge(['class' => 'form-control'], ['placeholder' => 'Provincia...'])) !!}
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 mt-1">
				{!! Form::text('city', old('city'), array_merge(['class' => 'form-control'], ['placeholder' => 'Ciudad...'])) !!}
			</div>
			<div class="col-sm-6 mt-1">
				{!! Form::text('address', old('address'), array_merge(['class' => 'form-control'], ['placeholder' => 'Dirección...'])) !!}
			</div>
		</div>
		{!! Form::number('cuit', old('cuit'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'CUIT...'])) !!}
		<div class="input-group mt-1">
			{!! Form::select('taxpayer_type_id', $taxpayer_type_id, old($supplier->taxpayer_type_id),['class'=>'form-control']) !!}
			<div class="input-group-append">
		    <span class="input-group-text">Tipo de contribuyente</span>
		  </div>
		</div>
		<div class="input-group mt-1">
			{!! Form::select('currency_id', $currencies, old($supplier->currency_id),['class'=>'form-control']) !!}
			<div class="input-group-append">
		    <span class="input-group-text">Moneda de facturación</span>
		  </div>
		</div>
		<div class="input-group mt-1">
			{!! Form::select('state_id', $states, old($supplier->state_id),['class'=>'form-control']) !!}
			<div class="input-group-append">
		    <span class="input-group-text">Estado del proveedor</span>
		  </div>
		</div>
		<br />
		<div id="contacts">
			<div class="contact input-group mt-1">
				{!! Form::text('contact[1][]', null, array_merge(['class' => 'form-control'], ['placeholder' => 'Nombre contacto...'])) !!}
				{!! Form::tel('contact[2][]', null, array_merge(['class' => 'form-control'], ['placeholder' => 'Teléfono...'])) !!}
				{!! Form::email('contact[3][]', null, array_merge(['class' => 'form-control'], ['placeholder' => 'Email...'], ['pattern' => '[^@]+@[^@]+\.[a-zA-Z]{2,6}'])) !!}
				<div class="input-group-append">
			    <span class="input-group-text d-2-none">Persona de contacto</span>
					<button class="btn btn-primary addContact" onclick="addContact()" title="Añadir otro contacto" type="button">+</button>
			  </div>
			</div>
			@foreach($supplier->contacts as $contact)
			<div class="input-group mt-1">
				{!! Form::text('contact[1][]', $contact->name, array_merge(['class' => 'form-control'], ['placeholder' => 'Nombre contacto...'])) !!}
				{!! Form::tel('contact[2][]', $contact->phone_number, array_merge(['class' => 'form-control'], ['placeholder' => 'Teléfono...'])) !!}
				{!! Form::email('contact[3][]', $contact->email, array_merge(['class' => 'form-control'], ['placeholder' => 'Email...'], ['pattern' => '[^@]+@[^@]+\.[a-zA-Z]{2,6}'])) !!}
				<div class="input-group-append">
					<span class="input-group-text d-2-none">Persona de contacto</span>
					<button class="btn btn-primary" onclick="removeContact(this)" title="Añadir otro contacto" type="button">-</button>
				</div>
			</div>
			@endforeach
		</div>
		<br />
		{!! Form::submit('Actualizar proveedor', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset('/suppliers')}}">Volver</a>
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
