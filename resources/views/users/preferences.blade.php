@extends('layouts.app')
@section('pageTitle')Configuración de preferencias - @endsection
@section('scripts')
<script type="application/javascript" src="{{ asset('js/User.js')}}"></script>
@endsection
@section('content')
<div class="container">
  <div class="card">
    <div class="card-header bg-dark text-light">Configuración de preferencias</div>
    <div class="card-body">
      <h5>Pagina de inicio</h5>
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="startPageSwitch" onchange="changeStartPage(this)" {{$user->config->show_panel_home ? "checked" : "" }}>
        <label class="custom-control-label" for="startPageSwitch">Panel / Tabla de proyectos</label>
      </div>
      @if(Auth::user()->permissionViewElements->state)
      <hr>
      <h5>General</h5>
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="generalElementSearchSwitch" onchange="changeShowGeneralElementSearch(this)" {{$user->config->show_element_general_search ? "checked" : "" }}>
        <label class="custom-control-label" for="generalElementSearchSwitch">Ocultar / Mostrar Buscador general de elementos al pie de la pantalla</label>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
