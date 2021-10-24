@extends('layouts.app')
@section('pageTitle'){{ "Crear material -" }} @endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Crear material</h1>
		<form action="/storematerial" method="post">
			@csrf
			<div class="form_group">
				<label for="materialName">Nombre</label>
				<input type="text" class="form-control" id="materialName" name="name" value="{{old('name')}}">
				@error('materialName')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<br/>
			<div class="form_group">
				<label for="materialInitials">Sigla que define al material <small>Máximo 8 caracteres</small></label>
				<input type="text" class="form-control" id="materialInitials" name="initials" value="{{old('initials')}}">
				@error('materialInitials')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<br/>
			<div class="form_group">
				<label for="materialDescription">Descripcion</label>
				<textarea class="form-control" id="materialDescription" name="description" value="{{old('description')}}">Ninguna</textarea>
				@error('materialDescription')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<br/>
			<div class="form_group">
				<label for="materialSpecificWeight">Peso específico [Kg/m<sup>3</sup>]</label>
				<input type="number" step="any" class="form-control" id="materialSpecificWeight" name="specific_weight" value="{{old('specific_weight')}}">
				@error('materialSpecificWeight')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<div class="form_group">
				<input type="hidden" class="form-control" id="materialStateId" name="state_id" value="1">
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
			<br/>
			<button type="submit" class="btn btn-primary">Submit</button>
		<form>
@endsection
