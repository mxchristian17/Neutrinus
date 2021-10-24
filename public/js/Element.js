$(document).ready(function(){
    if (typeof origName !== 'undefined')
    {
      if($('#element').val() == "") $('#element').val(origName);
    }

    if (typeof order_types !== 'undefined')
    {
      if(order_types[$("#order_type_id").val()][0]){$('#d_ext').show();$('label[for="d_ext"]').show();}else{$('#d_ext').hide();$('label[for="d_ext"]').hide();$('#d_ext').val(0);}
      if(order_types[$("#order_type_id").val()][1]){$('#d_int').show();$('label[for="d_int"]').show();}else{$('#d_int').hide();$('label[for="d_int"]').hide();$('#d_int').val(0);}
      if(order_types[$("#order_type_id").val()][2]){$('#side_a').show();$('label[for="side_a"]').show();}else{$('#side_a').hide();$('label[for="side_a"]').hide();$('#side_a').val(0);}
      if(order_types[$("#order_type_id").val()][3]){$('#side_b').show();$('label[for="side_b"]').show();}else{$('#side_b').hide();$('label[for="side_b"]').hide();$('#side_b').val(0);}
      if(order_types[$("#order_type_id").val()][4]){$('#large').show();$('label[for="large"]').show();}else{$('#large').hide();$('label[for="large"]').hide();$('#large').val(0);}
      if(order_types[$("#order_type_id").val()][5]){$('#width').show();$('label[for="width"]').show();}else{$('#width').hide();$('label[for="width"]').hide();$('#width').val(0);}
      if(order_types[$("#order_type_id").val()][6]){$('#thickness').show();$('label[for="thickness"]').show();}else{$('#thickness').hide();$('label[for="thickness"]').hide();$('#thickness').val(0);}
      if ($('#large').is(':visible')) {
          $('#shared_material_container').show();
          if (typeof editing_element === 'undefined') $('#shared_material').prop( "checked", true );
      } else {
          $('#shared_material_container').hide();
          if (typeof editing_element === 'undefined') $('#shared_material').prop( "checked", false );
      }
      $("#order_type_id").change(function(){
          if(order_types[$(this).children("option:selected").val()][0]){$('#d_ext').show();$('label[for="d_ext"]').show();}else{$('#d_ext').hide();$('label[for="d_ext"]').hide();$('#d_ext').val(0);}
          if(order_types[$(this).children("option:selected").val()][1]){$('#d_int').show();$('label[for="d_int"]').show();}else{$('#d_int').hide();$('label[for="d_int"]').hide();$('#d_int').val(0);}
          if(order_types[$(this).children("option:selected").val()][2]){$('#side_a').show();$('label[for="side_a"]').show();}else{$('#side_a').hide();$('label[for="side_a"]').hide();$('#side_a').val(0);}
          if(order_types[$(this).children("option:selected").val()][3]){$('#side_b').show();$('label[for="side_b"]').show();}else{$('#side_b').hide();$('label[for="side_b"]').hide();$('#side_b').val(0);}
          if(order_types[$(this).children("option:selected").val()][4]){$('#large').show();$('label[for="large"]').show();}else{$('#large').hide();$('label[for="large"]').hide();$('#large').val(0);}
          if(order_types[$(this).children("option:selected").val()][5]){$('#width').show();$('label[for="width"]').show();}else{$('#width').hide();$('label[for="width"]').hide();$('#width').val(0);}
          if(order_types[$(this).children("option:selected").val()][6]){$('#thickness').show();$('label[for="thickness"]').show();}else{$('#thickness').hide();$('label[for="thickness"]').hide();$('#thickness').val(0);}
          if ($('#large').is(':visible')) {
              $('#shared_material_container').show();
              $('#shared_material').prop( "checked", true );
          } else {
              $('#shared_material_container').hide();
              $('#shared_material').prop( "checked", false );
          }
      });
    };
    if (typeof $('#newElementDefinition') !== 'undefined')
    {
      $('label[for="element"]').show();
      $('#element').show();
      $('#elementHelp').show();
      $('#newElementDefinition').hide();
    };
});

function newElement(){
  event.preventDefault();
  if($("#element").is(":visible")){
    $('label[for="element"]').hide();
    $('#element').hide();
    $('#elementHelp').hide();
    $('#newElementDefinition').show();
    $('#newElementDefinitionButton').text('Utilizar un elemento general existente');
    $('#newType').val('1');
  }else{
    $('label[for="element"]').show();
    $('#element').show();
    $('#elementHelp').show();
    $('#newElementDefinition').hide();
    $('#newElementDefinitionButton').text('Crear nuevo elemento general');
    $('#name').val('');
    $('#newType').val('0');
  }
}

function editElement(id){
  location.href = "/editelement/" + id;
}

function deleteElement(element, name = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar el elemento general " + name)){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/deleteelement", { id: id, "_token": $('#tk').text(), })
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

function definitiveDeleteElement(element, name = 'none', project = 'none'){
  if (name=='none'){
    var id = element.parentNode.id;
    var table = element.parentNode.parentNode.parentNode;
    var row = element.parentNode.parentNode;
    var name = $(row.cells[0]).text();
  }else{
    var id = element;
    var name = name;
  }
  if(window.confirm("Seguro desea eliminar definitivamente el elemento " + name + ' del sistema? Esta acción no se puede deshacer y afectará a todas las aplicaciones del elemento en proyectos')){
    if (typeof table != "undefined" & table != null)
    {
      table.removeChild(row);
    }
    $.post( "/definitivedeleteelement", { id: id, "_token": $('#tk').text(), })
    .done(function( data ) {
      if (typeof table == "undefined" || table == null)
      {
        location.href = '/projects';
      };
    })
    .fail(function( data ) {
      alert( "Error al eliminar el elemento" + JSON.stringify(data, null, 4) );
    });
  }
}

function goToExt_f_1(A,B){
  var win = window.open(window.location.origin + '/element/ext_f_1/' + A + '/' + B, '_blank');
  if (!win) {
      alert('Por favor permita las ventanas emergentes para este sitio.');
  }
}
