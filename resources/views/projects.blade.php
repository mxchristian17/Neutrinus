@extends('layouts.app')
@section('pageTitle'){{"Proyectos -"}} @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Project.js')}}"></script>
@endsection
@section('content')
	<div class="container">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
				<h1>Proyectos</h1>
			@else

			@endauth
		</div>
		@endif
	@if(Auth::user()->permissionCreateProject->state)
	<a class="btn btn-link mr-2 mb-1 p-1" href="/createproject">Crear nuevo proyecto</a>
	@endif
	@if(Auth::user()->permissionCreateElement->state)
	<a class="btn btn-link mr-2 mb-1 p-1" href="/createelement">Crear nuevo elemento general</a>
	@endif
	@if((Auth::user()->permissionViewHiddenProjects->state) OR (Auth::user()->permissionViewDisabledProjects->state) OR (Auth::user()->permissionViewDeletedProjects->state))
		@if($showAll)
	<a href="/projects" class="btn btn-link p-1 float-right">Mostrar solo proyectos habilitados</a>
		@else
	<a href="/projects/1" class="btn btn-link p-1 float-right">Mostrar todos los proyectos</a>
		@endif
	@endif
	<input class="form-control" id="tableSearchInput" type="text" placeholder="Buscar...">
	<table id="projects" class="table table-hover">
		<thead class="thead-dark">
			<tr>
				<th onclick="sortTable('projects', 0)">Nombre</th>
				@if(Auth::user()->permissionViewProjectFolder->state)
				<th class="col-xs-1 text-center">Carpeta</th>
				@endif
				<th>Autor</th>
				<th>Estado</th>
				@if((Auth::user()->permissionCreateProject->state))
				<th></th>
				@endif
				@if((Auth::user()->permissionDeleteProject->state))
				<th></th>
				@endif
			</tr>
		</thead>
		<tbody id="tbodyProjects">
	@foreach ($projects as $project)
	@if(($project->states->id == 1 AND Auth::user()->permissionViewProjects->state) OR ($project->states->id == 2 AND Auth::user()->permissionViewDisabledProjects->state AND $showAll) OR ($project->states->id == 3 AND Auth::user()->permissionViewHiddenProjects->state AND $showAll) OR ($project->states->id == 4 AND Auth::user()->permissionViewDeletedProjects->state AND $showAll))
			<tr class="background-color-row-state-{{$project->states->id}}">
				<td><a href="/project/{{$project['id']}}" title="Proyecto para {{ Illuminate\Support\Str::lower($project->projecttypes->name) }}">{{$project->id}} - {{$project['name']}}</a></td>
				@if(Auth::user()->permissionViewProjectFolder->state)
				<td class="col-xs-1 text-center"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(1,{{$project->id}})" /></td>
				@endif
				<td>@can('seeUsersInformation')<a href="/user/{{$project->author->id}}">@endcan{{$project->author->name}}@can('seeUsersInformation')</a>@endcan</td>
				<td>{{$project->states->name}}</td>
				@if((Auth::user()->permissionCreateProject->state))
					<td><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editProject({{$project->id}})" /></td>
				@endif
				@if((Auth::user()->permissionDeleteProject->state))
					@if($project->state_id!=4)
				<td id="{{$project->id}}"><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteProject(this,'none')" /></td>
					@else
				<td id="{{$project->id}}"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteProject(this,'none')" /></td>
					@endif
				@endif
			</tr>
	@endif
	@endforeach
		</tbody>
	</table>
	@if(isset($project->links))
	{{$projects->links()}}
	@endif
	</div>
@endsection
