<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', ''){{ config('app.name', 'Neutrinus') }}</title>

    <!-- En esta posición estaban los scripts de app.js y jquery.min.js -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/neutrinus.css') }}" rel="stylesheet">
</head>
<body>
    <div id="tk" class="hide">{{ csrf_token() }}</div>
    <div id="app" class="pb-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ asset('/') }}">
                  <img src="{{asset('images/logo.png')}}" class="site_title_icon float-left" alt="Neutrinus" title="Neutrinus" />
                  <span class="align-baseline">eutrinus</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                      @if (Auth::check())
                        @if(Auth::user()->permissionViewSuppliers->state OR
                        Auth::user()->permissionViewClients->state OR
                        Auth::user()->permissionViewPurchase_Orders->state OR
                        Auth::user()->permissionViewSales->state OR
                        Auth::user()->permissionViewCash_Flow->state
                        )
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Administracion <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->permissionViewSuppliers->state)<a class="dropdown-item" href="/suppliers">Proveedores</a>@endif
                            @if(Auth::user()->permissionViewClients->state)<a class="dropdown-item" href="/clients">Clientes</a>@endif
                            @if(Auth::user()->permissionViewSales->state)<a class="dropdown-item" href="/sales">Ventas</a>@endif
                            @if(Auth::user()->permissionViewCash_Flow->state)<a class="dropdown-item" href="/cash_flow">Flujo de caja</a>@endif
                            <a class="dropdown-item" href="/purchasing_department">Compras</a>
                            @if(Auth::user()->permissionViewPurchase_Orders->state)<a class="dropdown-item" href="/purchase_orders">Órdenes de compras</a>@endif
                        </div>
                      </li>
                        @endif
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Técnica <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->permissionViewElements->state)<a class="dropdown-item" href="/elements">Tabla de elementos generales</a>@endif
                            <a class="dropdown-item" href="/cnc_programs">Programas CNC</a>
                        </div>
                      </li>
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Producción <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/deliver_equipment_table">Tabla de entrega de equipos</a>
                        </div>
                      </li>
                        @if(Auth::user()->permissionViewMaterials->state OR
                        Auth::user()->permissionViewMaterialPrices->state OR
                        Auth::user()->permissionViewOrder_types->state OR
                        Auth::user()->permissionViewOperations->state OR
                        Gate::allows('editPermissions')
                        )
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                          <img src="{{asset('images/configIcon.png')}}" class="bar_sup_icon d-2-none" alt="Configuración" title="Configuración" />
                          <span class="d-inline-block d-lg-none">Configuración</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                              @if(Auth::user()->permissionViewMaterials->state)<a class="dropdown-item" href="/materials">Materiales</a>@endif
                              @if(Auth::user()->permissionViewMaterialPrices->state)<a class="dropdown-item" href="/materialprices">Precio de materiales</a>@endif
                              @if(Auth::user()->permissionViewOrder_types->state)<a class="dropdown-item" href="/ordertypes">Tipos de pedido</a>@endif
                              @if(Auth::user()->permissionViewOperations->state)<a class="dropdown-item" href="/operation_names">Tipos de ruta</a>@endif
                              @if(Gate::allows('editPermissions'))<a class="dropdown-item" href="/userPermissionManager">Gestión de permisos de usuarios</a>@endif
                        </div>
                      </li>
                        @endif
                      @endif

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                          @if(Auth::user()->permissionViewProjects->state)
                            <li class="nav-item dropdown mr-2">
                              <a id="projectsButton" class="nav-link" href="{{asset('projects')}}" role="button">
                                <img src="{{asset('images/projectsIcon.png')}}" class="bar_sup_icon d-2-none" alt="Proyectos" title="Proyectos" />
                                <span class="d-inline-block d-lg-none">Proyectos</span>
                              </a>
                            </li>
                          @endif
                            <li class="nav-item dropdown mr-2">
                              <a id="panelButton" class="nav-link" href="{{asset('panel')}}" role="button">
                                <img src="{{asset('images/panelIcon.png')}}" class="bar_sup_icon d-2-none" alt="Panel" title="Panel" />
                                <span class="d-inline-block d-lg-none">Panel</span>
                              </a>
                            </li>
                          @if(Auth::user()->permissionCreatePurchase_Order->state)
                            <li class="nav-item dropdown mr-2">
                              <a id="shoppingCartDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <img src="{{asset('images/shoppingCartIcon.png')}}" class="bar_sup_icon d-2-none" alt="Carro de compras" title="Carro de compras" />
                                <span class="d-inline-block d-lg-none">Carro de compras</span><span class="badge badge-danger shoppingCartQty @if(Cart::count()==0) hide @endif ">({{Cart::count()}})</span>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{asset('shoppingcart')}}">Ver carro</a>
                              </div>
                            </li>
                          @endif
                          @if(Auth::user()->permissionUseReminders->state)
                            <li class="nav-item dropdown mr-2">
                              <a id="reminders" class="nav-link" href="#" role="button">
                                <img src="{{asset('images/reminderIcon.png')}}" class="bar_sup_icon d-2-none" alt="Recordatorios" title="Recordatorios" data-toggle="modal" data-target="#remindersModal" />
                                <span class="d-inline-block d-lg-none" data-toggle="modal" data-target="#remindersModal">Recordatorios</span><span id="countReminders" class="badge badge-danger @if(count($reminders)==0) hide @endif ">({{count($reminders)}})</span>
                              </a>
                            </li>
                          @endif
                          @if(Auth::user()->permissionUseTasks->state)
                            <li class="nav-item dropdown mr-2">
                              <a id="tasks" class="nav-link" href="#" role="button" onclick="$('#task_user_under_charge_{{auth()->user()->id}}').prop('checked', true);">
                                  <img src="{{asset('images/taskIcon.png')}}" class="bar_sup_icon d-2-none" alt="Tareas" title="Tareas" data-toggle="modal" data-target="#tasksModal" />
                                <span class="d-inline-block d-lg-none" data-toggle="modal" data-target="#tasksModal">Tareas</span><span id="countTasks" class="badge badge-danger @if(count($tasks)==0) hide @endif ">({{count($tasks)}})</span>
                              </a>
                            </li>
                          @endif
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} {{ Auth::user()->last_name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
									                  <a class="dropdown-item" href="/user/{{ Auth::user()->id }}">Perfil</a>
                                    <a class="dropdown-item" href="/preferences">Preferencias</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
                @if (Auth::check() AND Auth::user()->permissionViewElements->state AND Auth::user()->config->show_element_general_search AND Route::currentRouteName()!='elementSearch' AND Route::currentRouteName()!='createProjectElement' AND Route::currentRouteName()!='editProjectElement')
                <!-- INPUT SEARCH ELEMENT START -->
                <form id="elementSearchForm" class="d-2-none" action="{{asset('/searchelement')}}" method="GET" role="search" target="_blank">
                    {{ csrf_field() }}
                    <div class="form-group fixed-bottom m-0 w-25">
                      <div class="input-group input-group-sm dropup">
                        <input type="text" name="query" class="form-control" id="search_element_input" aria-describedby="searchElement" placeholder="Buscar elemento..." autocomplete="off">
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-dark rounded-right">
                              Buscar
                          </button>
                        </div>
                        <div id="elementIdList" class="autocomplete-items dropdown-menu">
            				    </div>
                      </div>
                    </div>
                </form>
                <!-- INPUT SEARCH ELEMENT END -->
                @endif
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- ALERT BOX START -->
      <div id="alertBox" class="fixed-bottom hide alert alert-danger font-weight-bold" style="z-index:1055;" role="alert"></div>
      @if(session()->has('message.level'))
      <div class="fixed-bottom alert alert-{{ session('message.level') }} alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {!! session('status') !!}
      </div>
      @endif
    <!-- ALERT BOX END -->

    <!-- CHAT START -->
    @if (Auth::check() AND Auth::user()->permissionUseChat->state AND (count($chatUsers)>0))
    <div id="chat_container" class="float-right d-print-none" style="z-index:1050;" >
      <div id="chat_sup_bar" class="rounded-top">
        <div id="chat_title" class="chat_title float-left p-1 pl-3" onclick="toggleChat()"></div>
        <div id="chat_start_message" class="d-none"></div>
        <div id="chat_last_message" class="d-none"></div>
        <div class="float-right p-1 pr-3 cursor-pointer" id="closeChatBtn" onclick="closeChat()">x</div>
        <!-- <div class="minimize_icon float-right p-1 pr-3 pl-3 cursor-pointer" title="Minimizar chat" id="minimizeChat" onclick="minimizeChat()">▾</div> -->
      </div>
      <div id="chat_content_container">
      </div>
      <div id="sendChatInput" class="sendChatInput">
        <div class="input-group">
          <textarea class="form-control " rows="1" autocomplete="off" name="chatInput" type="text" id="chatInput" placeholder="Escriba un mensaje..."></textarea>
          <div class="input-group-append">
            <span class="input-group-text p-1 pl-2 pr-2">
              <a href="#" data-toggle="popover" data-html="true" data-placement="auto" data-trigger="focus" title="" data-content="
              <span class='h5'>
                <a href='#' class='emoji'>&#x1f642;</a>
                <a href='#' class='emoji'>&#x1f603;</a>
                <a href='#' class='emoji'>&#x1f604;</a>
                <a href='#' class='emoji'>&#x1f605;</a>
                <a href='#' class='emoji'>&#x1f606;</a>
                <a href='#' class='emoji'>&#x1f607;</a>
                <a href='#' class='emoji'>&#x1f608;</a>
                <a href='#' class='emoji'>&#x1f624;</a>
                <a href='#' class='emoji'>&#x1f60D;</a>
                <a href='#' class='emoji'>&#x1f62D;</a>
                <a href='#' class='emoji'>&#x1f62E;</a>
                <a href='#' class='emoji'>&#x1f630;</a>
                <a href='#' class='emoji'>&#x1f633;</a>
                <a href='#' class='emoji'>&#x1f92C;</a>
                <a href='#' class='emoji'>&#x1f970;</a>
                <a href='#' class='emoji'>&#x1f60B;</a>
                <a href='#' class='emoji'>&#x1f611;</a>
                <a href='#' class='emoji'>&#x1f612;</a>
                <a href='#' class='emoji'>&#x1f915;</a>
                <a href='#' class='emoji'>&#x1f923;</a>
                <a href='#' class='emoji'>&#x1f924;</a>
                <a href='#' class='emoji'>&#x1f498;</a>
                <a href='#' class='emoji'>&#x1f44C;</a>
                <a href='#' class='emoji'>&#x1f91E;</a>
                <a href='#' class='emoji'>&#x1f446;</a>
                <a href='#' class='emoji'>&#x1f447;</a>
                <a href='#' class='emoji'>&#x1f44D;</a>
                <a href='#' class='emoji'>&#x1f44E;</a>
                <a href='#' class='emoji'>&#x1f44A;</a>
                <a href='#' class='emoji'>&#x1f44F;</a>
                <a href='#' class='emoji'>&#x1f64C;</a>
              </span>">&#x1f600;</a>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div id="chat_contacts_container">
      @foreach($chatUsers as $contact)
      <div class="chat_contact rounded-left @if(rand(0,1)) @endif" id="contact_{{$contact->id}}" onclick="openChat(this.id)">
        <a href="#" class="avatar rounded-circle m-1 float-left" data-toggle="popover" data-placement="auto" data-trigger="hover" title="" data-content="{{$contact->name}}">
          <img src="{{ route('avatarImg', $contact->id.'.jpg') }}" alt="Avatar" class="img img-responsive full-width">
        </a>
      </div>
      @endforeach
    </div>
    @endif
    <!-- CHAT END -->

    <!-- REMINDERS START -->
    @if (Auth::check())
    @if(Auth::user()->permissionUseReminders->state)
    <!-- Modal -->
    <div class="modal fade" id="remindersModal" tabindex="-1" role="dialog" aria-labelledby="Recordatorios" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Recordatorios</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="reminders_container">
            @foreach($reminders as $reminder)
              <div id="reminder_{{$reminder->id}}" class="container">
            		<span class="font-weight-bold">{{$reminder->title}} <small><span class="text-muted">{{\Carbon\Carbon::parse($reminder->reminder_date)->diffForHumans()}}</span></small> @if($reminder->new) <span class="text-danger">(nuevo) </span> @endif</span><br />
                @if(strlen($reminder->content))<div class="row mx-1"><div class="col text-dark rounded">{{$reminder->content}}</div></div>@endif
                <div class="row m-2">
                  <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                      <div class="input-group-text" id="btnGroupAddon">Posponer</div>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Postpone">
                      <button class="btn btn-secondary" onclick="postponeReminder(this, 1)">10min</button>
                      <button class="btn btn-secondary" onclick="postponeReminder(this, 2)">1hr</button>
                      <button class="btn btn-secondary" onclick="postponeReminder(this, 3)">1 día</button>
                      <button class="btn btn-secondary" onclick="postponeReminder(this, 4)">1 semana</button>
                      <button class="btn btn-danger btn-sm" onclick="cancelReminder(this)">Anular</button>
                      @if($reminder->repeat)
                      <button class="btn btn-danger btn-sm" onclick="cancelReminderForEver(this)">Eliminar</button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @if(!$loop->last)
            <hr>
            @endif
            @endforeach
            @if(count($reminders) == 0)
            <div class="container" id="no_reminders">No hay recordatorios activos</div>
            @endif
            <hr id="hr_new_reminders">
            <div id="newReminderForm" class="container">
              <div class="p-1 pl-3 rounded-sm bg-dark text-white">Crear nuevo recordatorio</div>
              {!! Form::text('title', old('title'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Título...'], ['autocomplete' => 'off'], ['id' => 'newReminderTitle'])) !!}
              {!! Form::textarea('content', old('content'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Contenido...'], ['rows' => '3'], ['id' => 'newReminderContent'])) !!}
              {!! Form::label('reminder_date', 'Fecha y hora estimada', ['class' => 'control-label']) !!}
              {!! Form::input('datetime-local', 'reminder_date', Carbon\Carbon::parse(now())->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
              <div class="p-3 pl-4 custom-control custom-checkbox" id="newReminderRepeat">
  							{{Form::hidden('repeat',0)}}
                {!! Form::checkbox('repeat', 1 ?? old('repeat'), false, array_merge(['class' => 'custom-control-input'], ['id' => 'repeat'], ['value' => 1])) !!}
                {!! Form::Label('repeat', 'Repetir', array_merge(['class' => 'custom-control-label'], ['onclick' => 'checkRepeatChecked()'])) !!}
  						</div>
              <div id="repeat_days_interval">
                {!! Form::label('repeat_days_interval', 'Repetir cada [días]', ['class' => 'control-label']) !!}
                <div class="input-group">
                  {!! Form::number('repeat_days_interval', old('repeat_days_interval') ?? 1, array_merge(['class' => 'form-control'], ['placeholder' => 'Repetir cada...'], ['step' => '1'], ['id' => 'newReminderRepeatDaysInterval']))!!}
            			<div class="input-group-append">
            		    <span class="input-group-text">días</span>
            		  </div>
            		</div>
              </div>
              <div class="row m-2">
                <div class="btn-group btn-group-sm" role="group" aria-label="Postpone">
                  <button class="btn btn-success" onclick="newReminder()">Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    @endif
    @endif
    <!-- REMINDERS END -->

    <!-- TASKS START -->
    @if (Auth::check())
    @if(Auth::user()->permissionUseTasks->state)
    <!-- Modal -->
    <div class="modal fade" id="tasksModal" tabindex="-1" role="dialog" aria-labelledby="Tareas" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tasksModalLabel">Tareas pendientes</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="tasks_container">
            @foreach($tasks as $task)
              <div id="task_{{$task->id}}" class="container p-0">
                <div class="row">
                  <div class="col-4 pr-0 m-0 arrow-right">
                		<a href="#" class="d-inline-block btn-primary text-truncate w-100 text-right pt-0 pb-0 px-2 m-0 rounded-left" style="height:1.4rem;" data-toggle="popover" data-placement="auto" data-trigger="hover" title="{{$task->title}}" data-content="@if(strlen($task->content)) <span class='btn btn-sm btn-primary'>{{$task->content}}</span><br /> @endif <b>Fecha estimada de inicio:</b> {{\Carbon\Carbon::parse($task->task_start)->diffForHumans()}}<br /><b>Fecha estimada de fin:</b> @if(\Carbon\Carbon::parse($task->task_estimated_end)->isPast()) <span class='text-danger'> @endif {{\Carbon\Carbon::parse($task->task_estimated_end)->diffForHumans()}} @if(\Carbon\Carbon::parse($task->task_estimated_end)->isPast()) </span> @endif<br /><span class='text-muted'>Tarea emitida por {{$task->author->name}}</span>">{{$task->title}} @if($task->new) <span class="text-danger">(nueva) </span> @endif</a>
                  </div>
                  <div class="col-4 h-100 px-0 m-0">
                    <div class="progress w-100" style="height:1.4rem; border-radius:0px;">
                      <div id="progressTaskBar_{{$task->id}}" class=" rounded-right progress-bar {{$task->bgColor}} progress-bar-animated" role="progressbar" style="width: {{$task->percentage}}%;" aria-valuenow="{{$task->percentage}}" aria-valuemin="0" aria-valuemax="100">{{$task->percentage}}%</div>
                    </div>
                  </div>
                <div class="col-4 h-100 pl-0 m-0">
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
            <hr id="task_hr_{{$task->id}}">
            @endif
            @endforeach
            @if(count($tasks) == 0)
            <div class="container" id="no_tasks">No hay tareas activas</div>
            @endif
            <hr id="hr_new_tasks">
            <div id="newTaskForm" class="container">
              <div class="p-1 pl-3 rounded-sm bg-dark text-white">Crear nueva tarea</div>
              @if (Auth::check() AND Auth::user()->permissionAssignTasks->state)
              <div class="row">
                <div class="col">
                  <div class="form-check mt-2">
                    {!! Form::radio('newTaskUser', Auth::user()->id, true, array_merge(['class' => 'form-check-input'], ['id' => 'task_user_under_charge_'.Auth::user()->id])) !!}
                    {!! Form::label('task_user_under_charge_'.Auth::user()->id, 'Para mi', ['class' => 'form-check-label']) !!}
                  </div>
                </div>
              </div>
                @foreach(auth()->user()->under_charge as $user_under_charge)
              <div class="row">
                <div class="col">
                  <div class="form-check">
                    {!! Form::radio('newTaskUser', $user_under_charge->user_under_charge->id, false, array_merge(['class' => 'form-check-input'], ['id' => 'task_user_under_charge_'.$user_under_charge->user_under_charge->id])) !!}
                    {!! Form::label('task_user_under_charge_'.$user_under_charge->user_under_charge->id, 'Para '.$user_under_charge->user_under_charge->name, ['class' => 'form-check-label']) !!}
                  </div>
                </div>
              </div>
                @endforeach
              @else
                {!! Form::hidden('newTaskUser', Auth::user()->id) !!}
              @endif
              {!! Form::text('title', old('title'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Título...'], ['autocomplete' => 'off'], ['id' => 'newTaskTitle'])) !!}
              {!! Form::textarea('content', old('content'), array_merge(['class' => 'form-control mt-1'], ['placeholder' => 'Contenido...'], ['rows' => '3'], ['id' => 'newTaskContent'])) !!}
              <div class="input-group input-group-sm mt-2">
                <!--{!! Form::label('task_date', 'Fecha y hora estimada de inicio', ['class' => 'control-label']) !!}-->
                <div class="input-group-prepend">
                  <span class="input-group-text">Inicio estimado</span>
                </div>
                {!! Form::input('datetime-local', 'task_date', Carbon\Carbon::parse(now())->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm mt-2">
                <!--{!! Form::label('task_end', 'Fecha y hora estimada de fin', ['class' => 'control-label']) !!}-->
                <div class="input-group-prepend">
                  <span class="input-group-text">Fin estimado</span>
                </div>
                {!! Form::input('datetime-local', 'task_end', Carbon\Carbon::parse(now()->addHours(2))->format('Y-m-d\TH:i'), ['class' => 'form-control']) !!}
              </div>
              <!--<div class="p-3 pl-4 custom-control custom-checkbox" id="newTaskRepeat">-->
  							{{Form::hidden('repeatTask',0)}}
                <!--{!! Form::checkbox('repeatTask', 1 ?? old('repeatTask'), false, array_merge(['class' => 'custom-control-input'], ['id' => 'repeatTask'], ['value' => 1])) !!}-->
                <!--{!! Form::Label('repeatTask', 'Repetir', array_merge(['class' => 'custom-control-label'], ['onclick' => 'checkRepeatTaskChecked()'])) !!}-->
  						<!--</div>-->
              <div id="repeat_task_days_interval">
                {!! Form::label('repeat_task_days_interval', 'Repetir cada [días]', ['class' => 'control-label']) !!}
                <div class="input-group">
                  {!! Form::number('repeat_task_days_interval', old('repeat_task_days_interval') ?? 1, array_merge(['class' => 'form-control'], ['placeholder' => 'Repetir cada...'], ['step' => '1'], ['id' => 'newTaskRepeatDaysInterval']))!!}
            			<div class="input-group-append">
            		    <span class="input-group-text">días</span>
            		  </div>
            		</div>
              </div>
              <div class="row m-2">
                <div class="btn-group btn-group-sm" role="group" aria-label="Postpone">
                  <button class="btn btn-success" onclick="newTask()">Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    @endif
    @endif
    <!-- TASKS END -->
    <!-- SCROLL TOP START -->
    <a id="back-to-top" href="#" class="btn btn-light btn-lg back-to-top" role="button">▲</a>
    <!-- SCROLL TOP END -->
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @if (Auth::check())
    <script type="text/javascript">
    var assetUrl = '{{asset("")}}';
    var useReminders = {{Auth::user()->permissionUseReminders->state}};
    var elementsUrl = "{{ route('autocompleteelement.fetch') }}";
    @if(Auth::user()->permissionUseReminders->state)
      var newReminders = {{$reminders->showAlert}};
      var countReminders = {{count($reminders)}};
      var minReminderId = {{$reminders->last()->id ?? 0}}
      var reminderAudio = new Audio("{{asset('audios/reminder.mp3')}}");
    @else
      var newReminders = false;
      var countReminders = 0;
    @endif
    var useTasks = {{Auth::user()->permissionUseTasks->state}};
    @if(Auth::user()->permissionUseTasks->state)
      var newTasks = {{$tasks->showAlert}};
      var countTasks = {{count($tasks)}};
      var minTaskId = {{$tasks->last()->id ?? 0}}
      var taskAudio = new Audio("{{asset('audios/task.mp3')}}");
    @else
      var newTasks = false;
      var countTasks = 0;
    @endif
    @if(Auth::user()->permissionUseChat->state)
      var url = "{{ route('showchat') }}";
      var urlb = "{{ route('checkunread') }}";
      var urlc = "{{ route('sendchat') }}";
      var chatEnabled = true;
      var chatAudio = new Audio("{{asset('audios/chat.mp3')}}");
    @else
      var url = "";
      var urlb = "";
      var urlc = "";
      var chatEnabled = false;
    @endif
      var contact = {@foreach($chatUsers as $contact) {{$contact->id}}:"{{$contact->name}}", @endforeach 0:"0"};
      var contact_ids = [@foreach($chatUsers as $contact){{$contact->id}}, @endforeach 0];
    </script>
    <script src="{{ asset('js/neutrinus.js') }}" defer></script>
    @endif
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    @yield('scripts')
</body>
</html>
