@extends('layouts.app')
@section('pageTitle'){{$user->name}} {{$user->last_name}} - @endsection
@section('scripts')
@can('editPermissions')
	<script type="text/javascript">var uid={{$user->id}}; var uname='{{$user->name}}';</script>
	<script type="application/javascript" src="{{ asset('js/User.js')}}"></script>
@endcan
	<script type="text/javascript">
		function editUser(id){
			location.href = "/edituser/" + id;
		}
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
		<div class="row justify-content-md-center">
    <div class="col col-lg-3 align-center">
			<div class="avatar avatar-big rounded-circle mx-auto mt-2 mb-2">
				<img src="{{ route('avatarImg', $user->id.'.jpg') }}" alt="Avatar" class="img img-responsive full-width">
			</div>
    </div>
    <div class="col-md-auto my-auto">
			<h1>{{$user->name}} {{$user->last_name}}</h1>
			@if((auth()->user()->id == $user->id OR Gate::allows('editUsers')))<a href="#" onclick="editUser({{$user->id}})">Editar perfil<img src="/images/editIcon.png" class="inline_icon enhance_hover pl-1" alt="Editar" title="Editar" /></a>@endif
			<ul class="nav navbar-nav">
				<li>Nombre: {{$user->name}} {{$user->last_name}}</li>
				<li>Email: {{$user->email}}</li>
				<li>Teléfono: {{$user->phone_number}}</li>
				@if((auth()->user()->id == $user->id OR Gate::allows('seeUsersInformation')))<li>Dirección: @if($user->address OR $user->city OR $user->country){{$user->address}}, {{$user->city}}, {{$user->country}}@endif</li> @endif
				<li>Sucursal de trabajo: {{$user->branch_office}}</li>
				@if((auth()->user()->id == $user->id OR Gate::allows('seeUsersInformation')))<li>Fecha de nacimiento: {{date('d/m/Y', strtotime($user->date_of_birth))}}</li> @endif
				<li>Nivel de acceso: {{$user->roles->first()->name}} - {{$user->roles->first()->description}}</li>
				<li>
					<div>
						@foreach($user->superiors as $coordinator)
						@if($loop->first)
						<span class="text-danger font-weight-bold">Superiores:</span>
						@endif
						{{$coordinator->user_at_charge->name}} {{$coordinator->user_at_charge->last_name}}@if(!$loop->last), @endif
						@endforeach
					</div>
				</li>
				<li>
					<div>
						@foreach($user->under_charge as $under_charge)
						@if($loop->first)
						<span class="text-danger font-weight-bold">Personal a cargo:</span>
						@endif
						{{$under_charge->user_under_charge->name}} {{$under_charge->user_under_charge->last_name}}@if(!$loop->last), @endif
						@endforeach
					</div>
				</li>
			</ul>
    </div>
  </div>
	@if(!is_null($userTasks))
	<hr>
	<div class="row justify-content-md-left">
		<div class="col-md-12">
			<h3>Tareas</h3>
			@foreach($userTasks as $task)
				<div id="task_user_{{$task->id}}" class="container p-0 m-1">
					<div class="row m-0 p-0">
						<div class="col-4 pr-0 m-0 arrow-right">
							<a href="#" class="d-inline-block btn-primary text-truncate w-100 text-right pt-0 pb-0 px-2 m-0 rounded-left" style="height:1.4rem;" data-toggle="popover" data-placement="auto" data-trigger="hover" title="{{$task->title}}" data-content="@if(strlen($task->content)) <span class='btn btn-sm btn-primary'>{{$task->content}}</span><br /> @endif <b>Fecha estimada de inicio:</b> {{\Carbon\Carbon::parse($task->task_start)->diffForHumans()}}<br /><b>Fecha estimada de fin:</b> @if(\Carbon\Carbon::parse($task->task_estimated_end)->isPast()) <span class='text-danger'> @endif {{\Carbon\Carbon::parse($task->task_estimated_end)->diffForHumans()}} @if(\Carbon\Carbon::parse($task->task_estimated_end)->isPast()) </span> @endif<br /><span class='text-muted'>Tarea emitida por {{$task->author->name}}</span>">{{$task->title}} @if($task->new) <span class="text-danger">(nueva) </span> @endif</a>
						</div>
						<div class="col h-100 px-0 m-0">
							<div class="progress w-100" style="height:1.4rem; border-radius:0px;">
								<div id="progressTaskBar_user_{{$task->id}}" class=" rounded-right progress-bar {{$task->bgColor}} progress-bar-animated" role="progressbar" style="width: {{$task->percentage}}%;" aria-valuenow="{{$task->percentage}}" aria-valuemin="0" aria-valuemax="100">{{$task->percentage}}%</div>
							</div>
						</div>
					<div class="col-auto h-100 mw-25 pl-0 m-0">
						<div class="input-group input-group-sm py-0" style="height:1.4rem;line-height:1.4rem;">
							<div class="btn-group btn-group-sm py-0" style="height:1.4rem;" role="group" aria-label="Progress">
								<div class="btn-group btn-group-sm">
									<button type="button" style="line-height:1rem;border-top-left-radius:0px;border-bottom-left-radius:0px;" class="btn btn-outline-primary" onclick="addTaskPercentage(this, 25, 1)">+25%</button>
									<button type="button" style="line-height:1rem;" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, 5)">+5%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, 10)">+10%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, 25)">+25%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, 50)">+50%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, 75)">+75%</a>
										<div role="separator" class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, -5)">-5%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, -10)">-10%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, -25)">-25%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, -50)">-50%</a>
										<a class="dropdown-item" href="#" onclick="addTaskPercentage(this, -75)">-75%</a>
									</div>
								</div>
								<button style="line-height:1rem;" class="btn btn-success btn-sm"  onclick="addTaskPercentage(this, 100, 2)" data-toggle="popover" data-placement="auto" data-trigger="hover" title="" data-content="Tarea finalizada">✓</button>
								<button style="line-height:1rem;" class="btn btn-dark btn-sm" onclick="cancelTask(this)" data-toggle="popover" data-placement="auto" data-trigger="hover" title="" data-content="Cancelar tarea">X</button>
								@if($task->repeat)
								<button class="btn btn-danger btn-sm" onclick="cancelTaskForEver(this)">Eliminar</button>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			@if(!$loop->last)
			<hr id="task_hr_user_{{$task->id}}">
			@endif
			@endforeach
			@if(count($userTasks) == 0)
			<div class="container" id="no_tasks">No hay tareas activas</div>
			@endif
			<button class="btn btn-primary mt-2" data-toggle="modal" data-target="#tasksModal" onclick="$('#task_user_under_charge_{{$user->id}}').prop('checked', true);">Asignar tarea</button>
		</div>
	</div>
	<hr>
	@endif
@can('editUserStatus')
		@if((intval($user->roles->pluck('id')[0])!=3) AND (Auth::user()->id != $user->id))
		<br />
		@can('deleteUsers')
		<h3>Estado de usuario</h3>
		<select class="form-control status" id="status" name='status'>
			<option value="1" @if(!$user->blocked_date)selected="selected"@endif >Habilitado</option>
			<option value="0" @if($user->blocked_date)selected="selected"@endif >Deshabilitado</option>
		</select>
		@endcan
		<h3 class="mt-3">Tipo de usuario</h3>
		<div>
			<div class="form-check">
				<input type="radio" class="form-check-input authLevel" value="1" id="radioUser" name='optRadio' @if(intval($user->roles->pluck('id')[0])==1) checked @endif>
				<label class="form-check-label" for="radioUser">Usuario normal</label>
			</div>
			<div class="form-check">
				<input type="radio" class="form-check-input authLevel" value="2" id="radioAdmin" name='optRadio' @if(intval($user->roles->pluck('id')[0])==2) checked @endif>
				<label class="form-check-label" for="radioAdmin">Administrador del sistema</label>
			</div>
		</div>
		@endif
@endcan
@can('editPermissions')
		<br />
		<h3>Gestion de permisos</h3>
		<hr/>
		<div class="mb-3">
			<h4 class="btn btn-danger d-inline" onclick="toggleTemplates();"><span class="plantilla">Mostrar</span><span style="display:none;" class="plantilla">Ocultar</span> Plantillas</h4>
			<a href="{{route('setpermissiontemplate', [$user->id, 1])}}" style="display:none;" class="btn btn-primary ml-2 plantilla">Administrador general</a>
			<a href="{{route('setpermissiontemplate', [$user->id, 2])}}" style="display:none;" class="btn btn-primary ml-2 plantilla">Ingeniería</a>
			<a href="{{route('setpermissiontemplate', [$user->id, 3])}}" style="display:none;" class="btn btn-primary ml-2 plantilla">Técnico de oficina Técnica</a>
			<a href="{{route('setpermissiontemplate', [$user->id, 4])}}" style="display:none;" class="btn btn-primary ml-2 plantilla">Ejecutivo de compras</a>
		</div>
		<hr/>
		<div>
			<h5>Proyectos</h5>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionViewProjects" name='1' {{ $user->permissionViewProjects->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionViewProjects">Permiso para ver proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledProjects" name='10' {{ $user->permissionViewDisabledProjects->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledProjects">Permiso para ver proyectos deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenProjects" name='11' {{ $user->permissionViewHiddenProjects->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenProjects">Permiso para ver proyectos ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedProjects" name='14' {{ $user->permissionViewDeletedProjects->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedProjects">Permiso para ver proyectos eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionCreateProject" name='2' {{ $user->permissionCreateProject->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionCreateProject">Permiso para crear y editar proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionDeleteProject" name='3' {{ $user->permissionDeleteProject->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionDeleteProject">Permiso para eliminar proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionViewProjectStats" name='63' {{ $user->permissionViewProjectStats->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionViewProjectStats">Permiso para ver estadísticas de proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionViewProjectFolder" name='84' {{ $user->permissionViewProjectFolder->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionViewProjectFolder">Permiso para ver carpeta de proyectos</label>
			</div>
			<br />
			<h5>Subconjuntos</h5>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionCreateSubset" name='15' {{ $user->permissionCreateSubset->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionCreateSubset">Permiso para crear y editar subconjuntos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteSubset" name='16' {{ $user->permissionDeleteSubset->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteSubset">Permiso para eliminar subconjuntos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledSubsets" name='17' {{ $user->permissionViewDisabledSubsets->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledSubsets">Permiso para ver subconjuntos deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenSubsets" name='18' {{ $user->permissionViewHiddenSubsets->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenSubsets">Permiso para ver subconjuntos ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedSubsets" name='19' {{ $user->permissionViewDeletedSubsets->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedSubsets">Permiso para ver subconjuntos eliminados</label>
			</div>
			<br />
			<h5>Elementos</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewElements" name='21' {{ $user->permissionViewElements->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewElements">Permiso para ver elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledElements" name='12' {{ $user->permissionViewDisabledElements->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledElements">Permiso para ver elementos deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenElements" name='13' {{ $user->permissionViewHiddenElements->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenElements">Permiso para ver elementos ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedElements" name='20' {{ $user->permissionViewDeletedElements->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedElements">Permiso para ver elementos eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionCreateElement" name='4' {{ $user->permissionCreateElement->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionCreateElement">Permiso para crear y editar elementos generales</label><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los elementos generales son elementos que no pertenecen a ningun proyecto en especial, pero se encuentran definidos en el sistema para poder incorporarse a los distintos proyectos. <a href='/help/elements' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" class="inline_icon pl-1 opacity-70"></a>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionCreateProjectelement" name='5' {{ $user->permissionCreateProjectelement->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionCreateProjectelement">Permiso para crear y editar elementos de proyecto</label><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los elementos de proyecto son los elementos generales que se incorporan a un proyecto en particular. <a href='/help/elements' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" class="inline_icon pl-1 opacity-70"></a>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateAppliedElement" name='5' {{ $user->permissionCreateAppliedElement->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateAppliedElement">Permiso para crear y editar elementos en curso</label><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Los elementos en curso son los elementos que corresponden a proyectos en curso. Es decir proyectos que se están ejecutando en este momento. <a href='/help/elements' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" class="inline_icon pl-1 opacity-70"></a>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionDeleteElement" name='6' {{ $user->permissionDeleteElement->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionDeleteElement">Permiso para eliminar elementos generales</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionDeleteProjectelement" name='7' {{ $user->permissionDeleteProjectelement->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionDeleteProjectelement">Permiso para eliminar elementos de proyecto</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionViewElementPrice" name='8' {{ $user->permissionViewElementPrice->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionViewElementPrice">Permiso para ver precios de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionEditElementPrice" name='9' {{ $user->permissionEditElementPrice->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionEditElementPrice">Permiso para editar precios de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionViewElementFolder" name='85' {{ $user->permissionViewElementFolder->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionViewElementFolder">Permiso para ver carpeta de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
  			<input type="checkbox" class="custom-control-input permission" id="permissionEditSupplier_code" name='88' {{ $user->permissionEditSupplier_code->state ? "checked" : "" }}>
  			<label class="custom-control-label" for="permissionEditSupplier_code">Permiso para editar codigos de proveedores</label>
			</div>
			<br />
			<h5>Materiales</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewMaterials" name='23' {{ $user->permissionViewMaterials->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewMaterials">Permiso para ver materiales</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledMaterials" name='24' {{ $user->permissionViewDisabledMaterials->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledMaterials">Permiso para ver materiales deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenMaterials" name='25' {{ $user->permissionViewHiddenMaterials->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenMaterials">Permiso para ver materiales ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedMaterials" name='26' {{ $user->permissionViewDeletedMaterials->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedMaterials">Permiso para ver materiales eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateMaterial" name='27' {{ $user->permissionCreateMaterial->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateMaterial">Permiso para crear y editar materiales</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteMaterial" name='28' {{ $user->permissionDeleteMaterial->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteMaterial">Permiso para eliminar materiales</label>
			</div>
			<br />
			<h5>Precios de materiales</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewMaterialPrices" name='65' {{ $user->permissionViewMaterialPrices->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewMaterialPrices">Permiso para ver precios de materiales</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateMaterialPrice" name='64' {{ $user->permissionCreateMaterialPrice->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateMaterialPrice">Permiso para cargar precios de materiales</label>
			</div>
			<br />
			<h5>Tipos de pedido</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewOrder_types" name='29' {{ $user->permissionViewOrder_types->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewOrder_types">Permiso para ver tipos de pedido</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledOrder_types" name='30' {{ $user->permissionViewDisabledOrder_types->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledOrder_types">Permiso para ver  tipos de pedido deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenOrder_types" name='31' {{ $user->permissionViewHiddenOrder_types->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenOrder_types">Permiso para ver  tipos de pedido ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedOrder_types" name='32' {{ $user->permissionViewDeletedOrder_types->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedOrder_types">Permiso para ver  tipos de pedido eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateOrder_type" name='33' {{ $user->permissionCreateOrder_type->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateOrder_type">Permiso para crear y editar  tipos de pedido</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteOrder_type" name='34' {{ $user->permissionDeleteOrder_type->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteOrder_type">Permiso para eliminar  tipos de pedido</label>
			</div>
			<br />
			<h5>Rutas</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewOperations" name='35' {{ $user->permissionViewOperations->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewOperations">Permiso para ver rutas</label><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Las rutas son los posibles caminos que recorrerá un elemento hasta estar dispuesto para la venta. En el caso de ser un elemento que se fabrica en la planta, un tipo de ruta puede ser corte a serrucho sin fin, otro puede ser Torno, otro pintura, etc... <a href='/help/operations' target='_blank' >Más información...</a>"><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewOperationPrice" name='35' {{ $user->permissionViewOperationPrice->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewOperationPrice">Permiso para ver el costo de las rutas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledOperations" name='36' {{ $user->permissionViewDisabledOperations->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledOperations">Permiso para ver rutas deshabilitadas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenOperations" name='37' {{ $user->permissionViewHiddenOperations->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenOperations">Permiso para ver rutas ocultas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedOperations" name='38' {{ $user->permissionViewDeletedOperations->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedOperations">Permiso para ver rutas eliminadas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateOperation" name='39' {{ $user->permissionCreateOperation->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateOperation">Permiso para crear y editar rutas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteOperation" name='40' {{ $user->permissionDeleteOperation->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteOperation">Permiso para eliminar rutas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewOperationFolder" name='86' {{ $user->permissionViewOperationFolder->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewOperationFolder">Permiso para ver carpeta de rutas</label>
			</div>
			<br />
			<h5>Proveedores</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewSuppliers" name='41' {{ $user->permissionViewSuppliers->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewSuppliers">Permiso para ver proveedores</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledSuppliers" name='42' {{ $user->permissionViewDisabledSuppliers->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledSuppliers">Permiso para ver proveedores deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenSuppliers" name='43' {{ $user->permissionViewHiddenSuppliers->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenSuppliers">Permiso para ver proveedores ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedSuppliers" name='44' {{ $user->permissionViewDeletedSuppliers->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedSuppliers">Permiso para ver proveedores eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateSupplier" name='45' {{ $user->permissionCreateSupplier->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateSupplier">Permiso para crear y editar proveedores</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteSupplier" name='46' {{ $user->permissionDeleteSupplier->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteSupplier">Permiso para eliminar proveedores</label>
			</div>
			<br />
			<h5>Clientes</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewClients" name='47' {{ $user->permissionViewClients->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewClients">Permiso para ver clientes</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledClients" name='48' {{ $user->permissionViewDisabledClients->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledClients">Permiso para ver clientes deshabilitados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenClients" name='49' {{ $user->permissionViewHiddenClients->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenClients">Permiso para ver clientes ocultos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedClients" name='50' {{ $user->permissionViewDeletedClients->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedClients">Permiso para ver clientes eliminados</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateClient" name='51' {{ $user->permissionCreateClient->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateClient">Permiso para crear y editar clientes</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteClient" name='52' {{ $user->permissionDeleteClient->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteClient">Permiso para eliminar clientes</label>
			</div>
			<br />
			<h5>Visualización de archivos</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewElementsExt_f_1" name='53' {{ $user->permissionViewElementsExt_f_1->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewElementsExt_f_1">Permiso para ver archivos PDF de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewSubsetsExt_f_1" name='54' {{ $user->permissionViewSubsetsExt_f_1->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewSubsetsExt_f_1">Permiso para ver archivos PDF de subconjuntos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewProjectsExt_f_1" name='55' {{ $user->permissionViewProjectsExt_f_1->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewProjectsExt_f_1">Permiso para ver PDF de proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewElementsExt_f_2" name='56' {{ $user->permissionViewElementsExt_f_2->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewElementsExt_f_2">Permiso para ver archivos de extension secundarios de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewSubsetsExt_f_2" name='57' {{ $user->permissionViewSubsetsExt_f_2->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewSubsetsExt_f_2">Permiso para ver archivos de extension secundarios de subconjuntos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewProjectsExt_f_2" name='58' {{ $user->permissionViewProjectsExt_f_2->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewProjectsExt_f_2">Permiso para ver archivos de extension secundarios de proyectos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewElementsExt_f_3" name='59' {{ $user->permissionViewElementsExt_f_3->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewElementsExt_f_3">Permiso para ver archivos de extension terciarios de elementos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewSubsetsExt_f_3" name='60' {{ $user->permissionViewSubsetsExt_f_3->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewSubsetsExt_f_3">Permiso para ver archivos de extension terciarios de subconjuntos</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewProjectsExt_f_3" name='61' {{ $user->permissionViewProjectsExt_f_3->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewProjectsExt_f_3">Permiso para ver archivos de extension terciarios de proyectos</label>
			</div>
			<br />
			<h5>Chat interno</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionUseChat" name='66' {{ $user->permissionUseChat->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionUseChat">Permiso para usar chat interno</label>
			</div>
			<br />
			<h5>Usuarios</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewUsersBaseInfo" name='73' {{ $user->permissionViewUsersBaseInfo->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewUsersBaseInfo">Permiso para ver información basica de usuarios</label><a href="#" class="table_icon d-2-none" data-toggle="popover" data-placement="auto" data-trigger="focus" title="Ayuda" data-content="Solo la información elemental como nombre, mail, teléfono, sucursal de trabajo y nivel de acceso a Neutrinus. Si eres administrador, siempre podrás ver esta información."><img src="/images/helpIcon.png" alt="Ayuda" title="Ayuda" class="inline_icon pl-1 opacity-70 align-top"></a>
			</div>
			<br />
			<h5>Órdenes de compras</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewPurchase_Orders" name='67' {{ $user->permissionViewPurchase_Orders->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewPurchase_Orders">Permiso para ver órdenes de compras</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewPurchase_OrderPrices" name='72' {{ $user->permissionViewPurchase_OrderPrices->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewPurchase_OrderPrices">Permiso para ver precios de órdenes de compras</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreatePurchase_Order" name='68' {{ $user->permissionCreatePurchase_Order->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreatePurchase_Order">Permiso para crear y editar órdenes de compras</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionAwardPurchase_Order" name='70' {{ $user->permissionAwardPurchase_Order->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionAwardPurchase_Order">Permiso para adjudicar órdenes de compras</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionReceivePurchase_Order" name='71' {{ $user->permissionReceivePurchase_Order->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionReceivePurchase_Order">Permiso para recibir órdenes de compras</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeletePurchase_Order" name='69' {{ $user->permissionDeletePurchase_Order->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeletePurchase_Order">Permiso para anular y eliminar órdenes de compras</label>
			</div>
			<br />
			<h5>Recordatorios</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionUseReminders" name='74' {{ $user->permissionUseReminders->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionUseReminders">Permiso para utilizar recordatorios</label>
			</div>
			<br />
			<h5>Tareas</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionUseTasks" name='75' {{ $user->permissionUseTasks->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionUseTasks">Permiso para utilizar servicio de tareas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionAssignTasks" name='76' {{ $user->permissionAssignTasks->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionAssignTasks">Permiso para asignar y remover tareas</label>
			</div>
			<br />
			<h5>Ventas</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewSales" name='77' {{ $user->permissionViewSales->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewSales">Permiso para ver ventas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDisabledSales" name='78' {{ $user->permissionViewDisabledSales->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDisabledSales">Permiso para ver ventas deshabilitadas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewHiddenSales" name='79' {{ $user->permissionViewHiddenSales->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewHiddenSales">Permiso para ver ventas ocultas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewDeletedSales" name='80' {{ $user->permissionViewDeletedSales->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewDeletedSales">Permiso para ver ventas eliminadas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionCreateSale" name='81' {{ $user->permissionCreateSale->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionCreateSale">Permiso para crear y editar ventas</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionDeleteSale" name='82' {{ $user->permissionDeleteSale->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionDeleteSale">Permiso para eliminar ventas</label>
			</div>
			<br />
			<h5>Flujos de caja</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewCash_Flow" name='87' {{ $user->permissionViewCash_Flow->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewCash_Flow">Permiso para ver flujo de caja</label>
			</div>
			<br />
			<h5>Produccion</h5>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input permission" id="permissionViewOwedItems" name='83' {{ $user->permissionViewOwedItems->state ? "checked" : "" }}>
				<label class="custom-control-label" for="permissionViewOwedItems">Permiso para ver elementos aún no entregados</label>
			</div>
		</div>
		<hr>
		@can('deleteUsers')
		<a class="btn btn-danger text-white" onclick="confirmUserDelete(event)" href="{{asset('deleteuser/'.$user->id)}}">Eliminar a {{$user->name}} {{$user->last_name}} de Neutrinus</a>
		@endcan
@endcan
@endsection
