$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyProjects tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function editProject(id){
  location.href = "/editproject/" + id;
}

function deleteProject(element, name = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar el proyecto " + name)){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/deleteproject", { id: id, "_token": $('#tk').text(), })
    .done(function( data ) {
      if (typeof table == "undefined" || table == null)
      {
        location.reload();
      };
    })
    .fail(function( data ) {
      alert( "Error al eliminar el proyecto" + JSON.stringify(data, null, 4) );
    });
  }
}

function definitiveDeleteProject(element, name = 'none', project = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar definitivamente el proyecto " + name + '? Esta acción no se puede deshacer')){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/definitivedeleteproject", { id: id, "_token": $('#tk').text(), })
    .done(function( data ) {
      if (typeof table == "undefined" || table == null)
      {
        location.href = '/project/' + project;
      };
    })
    .fail(function( data ) {
      alert( "Error al eliminar el proyecto");
    });
  }
}

function definitiveDeleteSubset(id, event) {
  if(!window.confirm('Seguro desea eliminar el subconjunto? Tenga en cuenta que esta acción no se puede deshacer.'))
  {
    event.preventDefault();
  }
}

function projectStats(id){
  location.href = "/projectstats/" + id;
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

function completeSubsetsSelector(id) {
  $('#ld_copyToSubsetSelector').show();
  $.post( "/completesubsetsselector", { id: id.value, "_token": $('#tk').text(), })
  .done(function( data ) {
    $('#copyTosubsetSelector').html(data);
    $('#ld_copyToSubsetSelector').hide();
  })
  .fail(function( data ) {
    alert( "Error al buscar subconjuntos del proyecto." + JSON.stringify(data, null, 4) );
    $('#ld_copyToSubsetSelector').hide();
  });
}

function addElementIdCopyTo(id,name) {
  $('#elementIdCopyTo').val(id);
  $('#elementNameCopyTo').text(name);
  return;
}

function addSubsetIdCopyTo(id,name) {
  $('#subsetIdCopyTo').val(id);
  $('#subsetNameCopyTo').text(name);
  return;
}
