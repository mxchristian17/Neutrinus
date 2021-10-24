$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyOperation_names tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function editOperation_name(id){
  location.href = "/editoperation_name/" + id;
}

function deleteOperation_name(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar la ruta " + $(row.cells[0]).text() + '?')){
    table.removeChild(row);
    $.post( "/deleteoperation_name", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar la ruta" + JSON.stringify(data, null, 4) );
    });
  }
}
function definitiveDeleteOperation_name(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar definitivamente la ruta " + $(row.cells[0]).text() + '? Esta acción no se puede deshacer')){
    table.removeChild(row);
    $.post( "/definitivedeleteoperation_name", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar la ruta" + JSON.stringify(data, null, 4) );
    });
  }
}

function editOperation(id){
  location.href = "/editoperation/" + id;
}
