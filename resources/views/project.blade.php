@extends('layouts.app')
@section('pageTitle'){{$project->name}} - @endsection
@section('scripts')
	<script type="application/javascript" src="{{ asset('js/Project.js')}}"></script>
	<script type="application/javascript" src="{{ asset('js/Projectelement.js')}}"></script>
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
		<h1 class="d-inline">{{$project->name}}</h1>
		@if((Auth::user()->permissionCreateProject->state))
		<img src="{{asset('/images/editIcon.png')}}" alt="Editar" title="Editar" class="inline_icon pl-1 mt-2 enhance_hover align-top" onclick="editProject({{$project->id}})" />
		@endif
		@if((Auth::user()->permissionViewProjectStats->state))
		<img src="{{asset('/images/statsIcon.png')}}" alt="Estadísticas del proyecto" data-toggle="tooltip" data-placement="bottom" title="Estadísticas del proyecto" class="title_bar_icon float-right pr-1 mt-2 enhance_hover align-top" onclick="projectStats({{$project->id}})" />
		@endif
		<h6>({{$project->projecttypes->name}})</h6>
		@if((Auth::user()->permissionCreateSubset->state))
		<a href="/createsubset/{{$project->id}}">Añadir subconjunto al proyecto</a>
		@endif
		@if((Auth::user()->permissionViewHiddenElements->state) OR (Auth::user()->permissionViewDisabledElements->state) OR (Auth::user()->permissionViewDeletedElements->state))
			@if($showAll)
		<a href="/project/{{$project->id}}" class="float-right">Mostrar solo elementos habilitados</a>
			@else
		<a href="/project/{{$project->id}}/1" class="float-right">Mostrar todos los elementos</a>
			@endif
		@endif
		<input id="tableSearchInput" type="text" placeholder="Buscar..." class="form-control">
		<br />
		<table id="projectelements" class="table table-hover">
			<thead class="thead-dark">
				<tr>
					<th title="Código único" onclick="sortTable('projectelements', 0)">Código</th>
					<th>Nombre</th>
					<th class="d-2-none">Subconjunto</th>
					@if(Auth::user()->permissionViewMaterials->state)<th>Material</th>@endif
					@if(Auth::user()->permissionViewOrder_types->state)<th class="d-4-none">Tipo pedido</th>@endif
					<th>Cantidad</th>
					<th class="d-3-none">Estado</th>
					@if((Auth::user()->permissionCreateProjectelement->state))
						<th class="d-2-none"></th>
					@endif
					@if((Auth::user()->permissionCreatePurchase_Order->state))
					<th class="d-4-none"></th>
					@endif
					@if(Auth::user()->permissionViewElementFolder->state)
					<th class="d-2-none"></th>
					@endif
					@if(Auth::user()->permissionViewElementsExt_f_1->state)
					<th>{{strtoupper(config('constants.ext_f_1'))}}</th>
					@endif
					@if(Auth::user()->permissionDeleteProjectelement->state)
					<th></th>
					@endif
					@if(((Auth::user()->permissionCreateProjectelement->state)) OR (Auth::user()->permissionCreateProjectelement->state) AND (Auth::user()->permissionCreateSubset->state))
						<th></th>
					@endif
				</tr>
			</thead>
			<tbody class="tbodyProjectelements">
				@foreach($subsets as $subset)
					@if(!($subset->state_id == 2 AND !Auth::user()->permissionViewDisabledSubsets->state) AND !($subset->state_id == 3 AND !Auth::user()->permissionViewHiddenSubsets->state) AND !($subset->state_id == 4 AND !Auth::user()->permissionViewDeletedSubsets->state))
				<tr>
					<td colspan="{{8+(Auth::user()->permissionCreateProjectelement->state)+(Auth::user()->permissionViewElementsExt_f_1->state)+(Auth::user()->permissionDeleteProjectelement->state)+(Auth::user()->permissionCreateProjectelement->state)+(Auth::user()->permissionViewElementFolder->state)}}"  @if($showAll) class="subset_col-dark px-2 py-1 m-0" @else class="subset_col px-2 py-1 m-0" @endif>
						<span class="pl-2 text-uppercase align-middle">{{$subset->name}}</span>
						@if(((Auth::user()->permissionCreateProjectelement->state)) OR (Auth::user()->permissionCreateProjectelement->state) AND (Auth::user()->permissionCreateSubset->state))
						<div class="dropdown float-right pr-2 py-0 m-0 align-middle">
							<div class="btn-group btn-group m-0 p-0">
								<button class="btn btn-sm btn-light dropdown-toggle py-0 m-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
									@if((Auth::user()->permissionCreateProjectelement->state))<a class="dropdown-item text-dark" title="Añadir elemento a {{$subset->name}}" href="/createprojectelement/{{$project->id}}/{{$subset->id}}"><img src="{{asset('/images/addIcon.png')}}" class="table_icon mr-2" alt="Añadir" title="Añadir" />Añadir elemento</a>@endif
									@if((Auth::user()->permissionCreateProjectelement->state) AND (Auth::user()->permissionCreateSubset->state))<a class="dropdown-item text-dark" href="#" data-toggle="modal" data-target="#copySubsetToModal" onclick="addSubsetIdCopyTo({{$subset->id}},'{{$subset->name}}')"><img src="{{asset('/images/copyIcon.png')}}" class="table_icon mr-2" alt="Copiar" title="Copiar" />Copiar a...</a>@endif
									@if(Auth::user()->permissionCreateSubset->state)<a class="dropdown-item text-dark" href="{{asset('/editsubset/'.$subset->id)}}"><img src="{{asset('/images/editIcon.png')}}" class="table_icon mr-2" alt="Editar" title="Editar" />Editar subconjunto</a>@endif
									@if(Auth::user()->permissionDeleteSubset->state)
									@if($subset->state_id!=4)
									<a class="dropdown-item text-dark" href="{{asset('/deletesubset/'.$subset->id)}}"><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteProjectelement(this,'none')" />Eliminar subconjunto</a>
										@else
									<a class="dropdown-item text-dark" href="{{asset('/definitivedeletesubset/'.$subset->id)}}" onclick="definitiveDeleteSubset({{$subset->id}},event)"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" />Eliminar definitivamente</a>
										@endif
									@endif
								</div>
							</div>
						</div>
						@endif
					</td>
				</tr>
						@foreach($subset->projectelement as $projectelement)
							@if(($projectelement->specific_state_id == 1 AND Auth::user()->permissionViewElements->state) OR ($projectelement->specific_state_id == 2 AND Auth::user()->permissionViewDisabledElements->state AND $showAll) OR ($projectelement->specific_state_id == 3 AND Auth::user()->permissionViewHiddenElements->state AND $showAll) OR ($projectelement->specific_state_id == 4 AND Auth::user()->permissionViewDeletedElements->state AND $showAll))
				<tr class="background-color-row-state-{{$projectelement->specific_state_id}}">
					<td title="{{str_pad($projectelement->project_id, 3, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->subset->subset_number, 2, '0', STR_PAD_LEFT)}}-{{str_pad($projectelement->part, 2, '0', STR_PAD_LEFT)}}-{{$projectelement->subpart}}-{{$projectelement->version}}">{{$projectelement->element->nro}}-{{$projectelement->element->add}}</td>
					<td><a href="/projectelement/{{$projectelement->id}}">{{$projectelement->element->name}}</a></td>
					<td class="d-2-none">{{$projectelement->subset->name}}</td>
					@if(Auth::user()->permissionViewMaterials->state)<td>{{$projectelement->element->material->initials}}</td>@endif
					@if(Auth::user()->permissionViewOrder_types->state)<td class="d-4-none">{{$projectelement->element->order_type->name}}</td>@endif
					<td>{{$projectelement->quantity}}</td>
					<td class="d-3-none"><span  class="btn-sm {{classForGeneralStateTitle($projectelement->specific_state_id)}}">{{$projectelement->specific_state->name}}</span></td>
					@if((Auth::user()->permissionCreateProjectelement->state))
						<td class="d-2-none"><img src="/images/editIcon.png" class="table_icon enhance_hover" alt="Editar" title="Editar" onclick="editProjectelement({{$projectelement->id}})" /></td>
					@endif
					@if((Auth::user()->permissionCreatePurchase_Order->state))
						<td class="d-4-none"><img class="table_icon" src="{{asset('images/shoppingCartIcon.png')}}" alt="Añadir al carro de compras" title="Añadir al carro de compras" onclick="addToShoppingCart('{{$projectelement->element->name}}', '{{$projectelement->element->id}}','{{$projectelement->quantity}}','{{$projectelement->project_id}}')" /></td>
					@endif
					@if(Auth::user()->permissionViewElementFolder->state)
						<td class=" d-2-none col-xs-1 text-center"><img src="{{asset('/images/folderIcon.png')}}" class="table_icon" alt="Abrir carpeta" title="Abrir carpeta" onclick="openFile(2,{{$projectelement->element->id}})" /></td>
					@endif
					@if(Auth::user()->permissionViewElementsExt_f_1->state)
						@if(file_exists(storage_path('app').'/files/ext_f_1/elements/'.$projectelement->element->nro.'-'.$projectelement->element->add.'.'.config('constants.ext_f_1')))
					<td class="text-center"><img src="{{asset('/images/pdfIcon.png')}}" class="table_icon" alt="{{strtoupper(config('constants.ext_f_1'))}} del elemento" title="{{strtoupper(config('constants.ext_f_1'))}} del elemento" onclick="goToExt_f_1('{{$projectelement->id}}','{{$projectelement->element->nro}}-{{$projectelement->element->add}} - {{$projectelement->element->name}}')" /></td>
						@else
					<td class="text-center"><img src="{{asset('/images/pdfIcon.png')}}" class="disabled_table_icon" alt="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" title="El elemento no posee {{strtoupper(config('constants.ext_f_1'))}} asociado" /></td>
						@endif
					@endif
					@if(Auth::user()->permissionDeleteProjectelement->state)
						@if($projectelement->specific_state->id!=4)
					<td id="{{$projectelement->id}}"><img src="{{asset('/images/trashIcon.png')}}" class="table_icon" alt="Borrar" title="Borrar" onclick="deleteProjectelement(this,'none')" /></td>
						@else
					<td id="{{$projectelement->id}}"><img src="{{asset('/images/trashRedIcon.png')}}" class="table_icon" alt="Borrar definitivamente" title="Borrar definitivamente" onclick="definitiveDeleteProjectelement(this,'none')" /></td>
						@endif
					@endif
					@if((Auth::user()->permissionCreateProjectelement->state))
						<td class="px-2">
							<div class="dropdown float-right pr-2 m-0">
								<div class="btn-group p-0 m-0">
									<button class="btn btn-sm btn-light dropdown-toggle py-0 m-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
								  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#copyToModal" onclick="addElementIdCopyTo({{$projectelement->id}},'{{$projectelement->element->name}}')"><img src="{{asset('/images/copyIcon.png')}}" class="table_icon mr-2" alt="Copiar" title="Copiar" />Copiar a...</a>
										@if((Auth::user()->permissionCreateProjectelement->state))<a class="dropdown-item text-dark d-block d-lg-none" title="Editar elemento" href="#" onclick="editProjectelement({{$projectelement->id}})"><img src="{{asset('/images/editIcon.png')}}" class="table_icon mr-2" alt="Editar" title="Editar" />Editar</a>@endif
								  </div>
							</div>
							</div>
						</td>
					@endif
				</tr>
							@endif
						@endforeach
					@endif
				@endforeach
			</tbody>
		</table>
	</div>
	<footer class="modal-footer">
		<div>Proyecto generado por {{$project->author->name}}</div>
	</footer>


@if(Auth::user()->permissionCreateProjectelement->state)
<!-- Modals -->
<div class="modal fade" id="copyToModal" tabindex="-1" role="dialog" aria-labelledby="Copiar a..." aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="copyToModalLabel">Copiar <span id="elementNameCopyTo"></span> a...</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="copyTo_container">
				<div id="copyToForm" class="container">
					<div class="p-1 pl-3 rounded-sm bg-dark text-white">Destino</div>
					{!! Form::open(['url' => '/elementcopyto']) !!}
					{!! Form::hidden('id', 0, ['id' => 'elementIdCopyTo']) !!}
					<div class="input-group my-1">
						<div class="input-group-prepend">
							<span class="input-group-text">Proyecto de destino</span>
						</div>
						{!! Form::select('project_id', $copyToProjects, 0,array_merge(['class'=>'form-control'], ['onchange' => 'completeSubsetsSelector(this)'])) !!}
					</div>
					<div class="input-group my-1">
						<div class="input-group-prepend">
							<span class="input-group-text">Subconjunto de destino</span>
						</div>
						<select name="subset_id" id="copyTosubsetSelector" class="form-control">
							<option></option>
						</select>
					</div>
					<div class="row py-1 hide" id="ld_copyToSubsetSelector">
						<div class="col w-100 align-self-center text-center">
							<img class="f-right table_icon mx-2 align-middle" src="{{asset('/images/loading.gif')}}" /><span class="align-middle">Cargando...</span>
						</div>
					</div>
					{!! Form::submit('Generar copia', ['class' => 'form-control btn btn-primary']) !!}
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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
@endif

@if(Auth::user()->permissionCreateProjectelement->state AND Auth::user()->permissionCreateSubset->state)
<div class="modal fade" id="copySubsetToModal" tabindex="-1" role="dialog" aria-labelledby="Copiar a..." aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="copySubsetToModalLabel">Copiar <span id="subsetNameCopyTo"></span> a...</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="copySubsetTo_container">
				<div id="copySubsetToForm" class="container">
					<div class="p-1 pl-3 rounded-sm bg-dark text-white">Destino</div>
					{!! Form::open(['url' => '/subsetcopyto']) !!}
					{!! Form::hidden('id', 0, ['id' => 'subsetIdCopyTo']) !!}
					<div class="input-group my-1">
						<div class="input-group-prepend">
							<span class="input-group-text">Proyecto de destino</span>
						</div>
						{!! Form::select('project_id', $copyToProjects, 0,array_merge(['class'=>'form-control'], ['onchange' => 'completeSubsetsSelector(this)'])) !!}
					</div>
					{!! Form::submit('Generar copia', ['class' => 'form-control btn btn-primary']) !!}
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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
@endif

@endsection
