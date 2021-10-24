@extends('layouts.app')
@section('pageTitle'){{ "Ayuda > Tipos de pedido -" }} @endsection
@section('content')
  <div class="container">
    @if (Route::has('login'))
    <div class="top-right links">
      @auth
      @else

      @endauth
    </div>
    @endif
    <h1>Ayuda - Rutas</h1>
    <p>
      En Neutrinus, sobre los elementos generales se puede definir rutas que componen el proceso de fabricación u obtención de los mismos.<br />
      Cada ruta se asigna en forma ordenada desde la primera hasta la última que se ejecuta, y que finalmente se traduce en  la obtención del elemento finalizado.
      Por ejemplo, para fabricar una columna cilíndrica de acero a partir de una barra de material, se definirán como rutas:
      <ul class="list-group">
        <li class="list-group-item"><span class="font-weight-bold mr-2">1°</span> Corte del material con serrucho <small class="text-secondary">(donde la ruta sería corte con serrucho sin fin)</small></li>
        <li class="list-group-item"><span class="font-weight-bold mr-2">2°</span> Torneado de la pieza en torno CNC <small class="text-secondary">(donde la ruta sería torno cnc)</small></li>
        <li class="list-group-item"><span class="font-weight-bold mr-2">3°</span> Roscado de extremos de columna <small class="text-secondary">(donde la ruta sería ajuste)</small></li>
        <li class="list-group-item"><span class="font-weight-bold mr-2">4°</span> Cromado de la columna <small class="text-secondary">(donde la ruta sería cromado)</small></li>
      </ul>
      <br />
      Cada una de las rutas tendrá asignado un <span class="text-danger font-weight-bold">tiempo de preparación</span> y un <span class="text-danger font-weight-bold">tiempo de fabricación</span>.<br />
      El tiempo de preparación corresponde al tiempo que se demora en preparar una máquina para iniciar el proceso de fabricación, y que además se hace una sola vez por cada serie de piezas que se fabrican. Por lo que el costo de ese tiempo se dividirá entre todas las piezas de la serie.<br />
      Por otra parte el tiempo de mecanizado es el que corresponde a todas las tareas que se deben repetir integramente para cada pieza que se fabrica.<br />
      Un ejemplo para el caso  del torno cnc, puede ser 10 minutos de preparación de la máquina y 15 minutos de mecanizado por cada pieza de la serie. En el caso de una serie de 10 piezas, Neutrinus contemplará que el costo correspondiente al tiempo de mecanizado de cada pieza será de 10 minutos de preparación / 10 piezas = 1 minuto, sumado a 15 minutos de mecanizado de cada pieza. Por lo que el costo de tiempo por pieza será de 16 minutos.<br />
      Además, desde la tabla de <a href="{{asset('/operation_names')}}" target="_blank">Tipos de ruta</a> se puede configurar el <span class="text-danger font-weight-bold">costo directo en USD por hora</span> de la ruta definida. Con lo cual Neutrinus estimará el costo monetario de fabricar la pieza incluyendo el correspondiente a los tiempos de fabricación. La configuración de este valor es muy importante dado que es de <span class="text-danger font-weight-bold">fundamental</span> importancia al momento de estimar los costos totales de un proyecto.
    </p>
    <p>
      Cada ruta asignada a un elemento, además tiene un parámetro definido con el nombre de programa. El mismo fue pensado para las rutas de control numérico o similares, donde se debe guardar un programa asignado a la ruta.<br />
      Por ejemplo en el caso de la columna que se comentó más arriba, podría ser que el programa de control númerico asignado fuera el "PG00154.cnc". Neutrinus asignará una carpeta en la cual se deberá guardar el programa de manera tal que permita un rapido y fácil acceso al mismo.
    </p>
  </div>
@endsection
