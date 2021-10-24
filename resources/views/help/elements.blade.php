@extends('layouts.app')
@section('pageTitle'){{ "Ayuda > Elementos -" }} @endsection
@section('content')
  <div class="container">
    @if (Route::has('login'))
    <div class="top-right links">
      @auth
      @else

      @endauth
    </div>
    @endif
    <h1>Ayuda - Elementos</h1>
    <p>
      En Neutrinus, los proyectos se encuentran divididos en subconjuntos y los subconjuntos están compuestos por elementos.<br />
      Los elementos tienen propiedades propias como puede ser su material (por ejemplo Acero SAE 1010), la forma en que se pide el material (Por ejemplo en Barra trafilada), etc...<br />
      Por otro lado, los elementos tienen propiedades que corresponden a su aplicación en un proyecto general. Cómo puede ser la cantidad de veces que va el elemento en ese proyecto puntual. Un ejemplo práctico puede ser que un proyecto sea la fabricación de una mesa de 4 patas. El elemento sería Pata de la mesa y la cantidad sería 4.<br />
      Pero también puede darse el caso de un proyecto que sea una mesa de 3 patas, donde las patas sean exactamente iguales a las patas que se usa en el proyecto de la mesa de 4 patas. En este caso, la cantidad para el proyecto sería 3, pero el elemento Pata de la mesa sigue siendo el mismo.<br />
      De este modo hasta el momento tenemos definidos los elementos generales (con sus propiedades propias) y los elementos de proyecto (que son un elemento general aplicado a un proyecto con propiedades que lo definen en el proyecto).<br />
      Los proyectos en Neutrinus, a su vez pueden ser proyectos generales o proyectos en curso. Los proyectos generales son la definición de un proyecto que puede ejecutarse en cualquier momento. Mientras que los proyectos en curso son proyectos generales que se están ejecutando en este preciso momento.<br />
      Teniendo en cuenta los conceptos previos. Nos encontramos con un nuevo tipo de elemento que son los elementos en curso. Estos son elementos de proyecto, que se encuentran en un proyecto que se encuentra siendo ejecutado. La particularidad de este tipo de elementos es que se puede configurar su estado entre los siguientes:
      <ul>
        <li>Aprobar diseño</li>
        <li>Pedir material</li>
        <li>Material pedido</li>
        <li>Material en taller</li>
        <li>Pieza fabricada</li>
        <li>Pieza montada</li>
      </ul>
    </p>
    <h2>Ejemplo de funcionamiento de los elementos</h2>
    <h5>Definición</h5>
    <table class="table text-center">
      <thead>
        <th>Elementos generales</th>
        <th></th>
        <th>Elementos de proyecto</th>
        <th></th>
        <th>Elementos en curso </th>
      </thead>
      <tbody>
        <tr>
          <td class="align-middle">Elemento general<br /><small>(No tiene proyecto)</small></td>
          <td class="align-middle">►</td>
          <td class="align-middle">Elemento de proyecto <small>(Proyecto 1)</small><br />
            Elemento de proyecto <small>(Proyecto 2)</small><br />
            Elemento de proyecto <small>(Proyecto ...)</small><br />
            Elemento de proyecto <small>(Proyecto n)</small><br />
          </td>
          <td class="align-middle">►</td>
          <td class="align-middle">
            Elemento en curso <small>(para proyecto 2 en curso)</small><br />
            Elemento en curso <small>(para proyecto 5 en curso)</small><br />
          </td>
        </tr>
        <tr>
          <td class="align-middle">▲<br />Propiedades<br />▼</td>
          <td class="align-middle"></td>
          <td class="align-middle">▲<br />Propiedades<br />▼</td>
          <td class="align-middle"></td>
          <td class="align-middle">▲<br />Propiedades<br />▼</td>
        </tr>
        <tr>
          <td class="align-middle">Material, Tipo de pedido, stock, precio de material unitario, etc...</td>
          <td class="align-middle"></td>
          <td class="align-middle">Datos del proyecto, Cantidad, Vinculaciones con otros elementos, Número de versión, Órden de pedido de material y Órden de fabricación</td>
          <td class="align-middle"></td>
          <td class="align-middle">Estado de pieza actual (Revisar diseño, Pedir material, ...)</td>
        </tr>
      </tbody>
    </table>
  </div>
@endsection
