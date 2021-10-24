@extends('layouts.app')
@section('pageTitle'){{ "Editar perfil de $user->name $user->last_name -" }} @endsection
@section('scripts')
<script type="text/javascript">
//START PREVIEW AVATAR
var loadFile = function(event) {
  var output = document.getElementById('output');
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
		URL.revokeObjectURL(output.src) // free memory
		if(this.width > this.height)
		{
			$('#changeAvatar img').css('height', this.height*(this.width/this.height));
		}else{
			$('#changeAvatar img').css('width', this.width*(this.height/this.width));
		}
  }
};
//END PREVIEW AVATAR

//START ADD AND REMOVE RELATIONS
@can('editUserStatus')
    @if(intval(auth()->user()->roles->pluck('id')[0])!=1)
var uid={{$user->id}}; var uname='{{$user->name}}';
$(document).ready(function(){
  $('.atCharge').change(function() {
      if(this.checked) { var state = 1; }else{ var state = 0; }
      var cid = $(this).prop("name");
      var id = $(this).prop("id");
      $.post( "/editUserAtCharge", { uid: uid, atChargeid: id, "_token": $('#tk').text(), state: state })
      .done(function( data ) {
        alert($("[for=" + id + "]").text() + " " + data);
      })
      .fail(function( data ) {
        alert( "Error en la asignación de cargo" + JSON.stringify(data, null, 4) );
      });
  });
});
    @endif
@endcan

//END ADD AND REMOVE RELATIONS
</script>
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
		<h1>Editar a {{$user->name}} {{$user->last_name}}</h1>
    {!! Form::model($user, [
		    'method' => 'POST',
		    'route' => ['updateuser', $user->id],
				'class' => 'mt-2',
				'enctype' => 'multipart/form-data'
		]) !!}
		<div class="form-group">
			<div class="avatar-l rounded-circle m-2" id="changeAvatar">
				<img src="{{ route('avatarImg', $user->id.'.jpg') }}" alt="Foto de perfil" id="output" style="max-height:6em;" />
			</div>
        {!! Form::label('image', 'Foto de perfil', ['class' => 'control-label']) !!}
        {!! Form::file('image', ['onchange' => 'loadFile(event)']) !!}
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Nombre', ['class' => 'control-label']) !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('last_name', 'Apellido', ['class' => 'control-label']) !!}
        {!! Form::text('last_name', old('last_name'), ['class' => 'form-control']) !!}
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('email', 'Correo electrónico', ['class' => 'control-label']) !!}
        {!! Form::text('email', old('email'), ['class' => 'form-control']) !!}
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('branch_office', 'Sucursal', ['class' => 'control-label']) !!}
        {!! Form::text('branch_office', old('branch_office'), ['class' => 'form-control']) !!}
        @error('branch_office')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('address', 'Dirección', ['class' => 'control-label']) !!}
        {!! Form::text('address', old('address'), ['class' => 'form-control']) !!}
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('city', 'Ciudad', ['class' => 'control-label']) !!}
        {!! Form::text('city', old('city'), ['class' => 'form-control']) !!}
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('country', 'Pais', ['class' => 'control-label']) !!}
        {!! Form::text('country', old('country'), ['class' => 'form-control']) !!}
        @error('country')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('phone_number', 'Número de teléfono', ['class' => 'control-label']) !!}
        {!! Form::tel('phone_number', old('phone_number'), ['class' => 'form-control']) !!}
        @error('phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        {!! Form::label('date_of_birth', 'Fecha de nacimiento', ['class' => 'control-label']) !!}
        {{ Form::date('date_of_birth', old('date_of_birth', date('Y-m-d', strtotime($user->date_of_birth))), ['class' => 'form-control']) }}
        @error('date_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @can('editUserStatus')
    		@if(intval(auth()->user()->roles->pluck('id')[0])!=1)
    <br />
    <div class="mb-2" id="relations">
      <h3>Personal a cargo</h3>
      @foreach($users as $userData)
      <div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input atCharge" id="atCharge{{$userData->id}}" name='{{$userData->id}}' {{ $userData->atCharge ? "checked" : "" }} {{ $userData->overCharge ? "disabled" : "" }}>
  			<label class="custom-control-label" for="atCharge{{$userData->id}}">{{$userData->name}} {{$userData->last_name}}</label>
			</div>
      @endforeach
		</div>
        @endif
    @endcan
    @if ($errors->any())
				<div class="alert alert-danger">
						<ul>
								@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
								@endforeach
						</ul>
				</div>
		@endif
    {!! Form::submit('Actualizar perfil', ['class' => 'btn btn-primary']) !!}
		<a class="btn btn-link" href="{{asset("/user/$user->id")}}">Volver</a>
    {{ Form::close() }}
@endsection
