$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyMaterials tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function editMaterial(id){
  location.href = "/editmaterial/" + id;
}

function deleteMaterial(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar el elemento " + $(row.cells[0]).text() + '?')){
    table.removeChild(row);
    $.post( "/deletematerial", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar el material" + JSON.stringify(data, null, 4) );
    });
  }
}
function definitiveDeleteMaterial(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar definitivamente el elemento " + $(row.cells[0]).text() + '? Esta acción no se puede deshacer')){
    table.removeChild(row);
    $.post( "/definitivedeletematerial", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar el material" + JSON.stringify(data, null, 4) );
    });
  }
}
