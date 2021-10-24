$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".tbodyProjectelements tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function editProjectelement(id){
  location.href = "/editprojectelement/" + id;
}

function deleteProjectelement(element, name = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar el elemento " + name)){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/deleteprojectelement", { id: id, "_token": $('#tk').text(), })
    .done(function( data ) {
      if (typeof table == "undefined" || table == null)
      {
        location.reload();
      };
    })
    .fail(function( data ) {
      alert( "Error al eliminar el elemento" + JSON.stringify(data, null, 4) );
    });
  }
}

function definitiveDeleteProjectelement(element, name = 'none', project = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar definitivamente el elemento " + name + ' de este proyecto? Esta acción no se puede deshacer')){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/definitivedeleteprojectelement", { id: id, "_token": $('#tk').text(), })
    .done(function( data ) {
      if (typeof table == "undefined" || table == null)
      {
        location.href = '/project/' + project;
      };
    })
    .fail(function( data ) {
      alert( "Error al eliminar el elemento" + JSON.stringify(data, null, 4) );
    });
  }
}

function goToExt_f_1(A,B){
  var win = window.open(window.location.origin + '/ext_f_1/' + A + '/' + B, '_blank');
  if (!win) {
      alert('Por favor permita las ventanas emergentes para este sitio.');
  }
}
