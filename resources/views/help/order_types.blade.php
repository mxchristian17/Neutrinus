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
    <h1>Ayuda - Tipos de pedido</h1>
    <p>
      En Neutrinus, los elementos generales tienen como una de sus propiedades, el tipo de pedido de material.<br />
      Dicha propiedad refiere a la forma en que se hará el pedido del material correspondiente al elemento. A su vez, el tipo de pedido tiene asociadas en el caso de corresponder, medidas que lo definen y luego permiten a Neutrinus calcular el volumen y peso del elemento.<br />
      Un ejemplo de lo antes expuesto podría ser el caso de un elemento que fuera un barrote de una reja de una casa. El tipo de pedido que le correspondería sería "Barra cuadrada", "Barra cilindrica" o "Caño" según el tipo de reja del que estemos hablando.<br />
      Otro ejemplo podría ser una puerta de un mueble de melamina, donde el tipo de pedido sería "Placa" o "Chapa" y el material "Melamina".<br />
      Existe también la posibilidad de utilizar un tipo de pedido con el nombre "Normalizado", que podría corresponder por ejemplo a un elemento que sea un Motor Eléctrico, o una Computadora, o un PLC, etc... Donde no corresponde ninguna geometría estandar en especial.<br />
      Neutrinus utilizará la información del tipo de pedido y las dimensiones asignadas a un elemento para calcular, en caso de ser posible, el volúmen del material correspondiente al elemento. Y al combinar esta información con el peso especifico del material asignado, podrá calcular el peso del elemento.<br />
      El peso del elemento es un parámetro fundamental a la hora de calcular los costos de un proyecto completo dado que con el peso y el valor monetario por unidad de peso que se encuentre configurado en Neutrinus, se puede saber el costo monetario del material de una pieza.
    </p>
    <p>
      Al crear un tipo de pedido, se deben definir los parámetros qué definen a la geometría del tipo de pedido. Los mismos pueden ser:
      <ul>
        <li>Diámetro exterior</li>
        <li>Diámetro interior</li>
        <li>Lado A</li>
        <li>Lado B</li>
        <li>Largo</li>
        <li>Ancho</li>
        <li>Espesor</li>
      </ul>
      Una vez definidos los parámetros que caracterizan al tipo de pedido, se puede definir una fórmula para el cálculo de su volúmen.
    </p>
    <p>
      Un ejemplo práctico de lo antes expuesto puede ser el siguiente:<br/>
      Supongamos que vamos a definir el tipo de pedido "Barra cilíndrica".<br />
      Los parámetros que definen una barra cilíndrica son su diámetro exterior y su largo. Por lo tanto, entre las opciones de creación del tipo de pedido, vamos a tildar los casilleros de Diámetro exterior y Largo.<br />
      Por otro lado, la fórmula para calcular el volúmen de una barra cilíndrica, es:<br /><br />
      Volúmen = π*(Øexterior/2)<sup>2</sup><br /><br />
      Por lo que eso mismo será lo que completemos en el asistente para creación de fórmulas que se encuentra en el creador de tipos de pedido.<br />
      En caso de no crear ninguna fórmula para el tipo de pedido, simplemente, lo que ocurrirá es que Neutrinus no podrá hacer una estimación de volumen, peso, ni costo por unidad de peso para todos los elementos que tengan el tipo de pedido que se define. Pero no habrá ningún inconveniente en la operación del sistema.
    </p>
  </div>
@endsection
