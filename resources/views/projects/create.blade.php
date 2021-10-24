@extends('layouts.app')
@section('pageTitle'){{ "Crear proyecto -" }} @endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			@else

			@endauth
		</div>
		@endif
		<h1>Crear Proyecto</h1>
		<form action="/storeproject" method="post">
			@csrf
			<div class="form_group">
				<label for="projectName">Nombre</label>
				<input type="text" class="form-control" id="projectName" name="name">
				@error('projectName')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<br/>
			<div class="form_group">
				<label for="projectType">Tipo de proyecto</label>
				<select class="form-control" id="projectType" name="type">
					@foreach($projecttypes as $projecttype)
					<option value="{{$projecttype->id}}">{{$projecttype->name}}</option>
					@endforeach
				</select>
				@error('projectDescription')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
			</div>
			<input type="hidden" class="form-control" id="projectState" name="state_id" value="1">
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
