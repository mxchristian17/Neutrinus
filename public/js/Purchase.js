$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyPurchase_orders tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function editPurchase_order(id){
  location.href = "/editpurchase_order/" + id;
}

function upgradePurchase(id, rq){
  var qt = null;
  if((orders_status[id] == 0) && (rq != 6))
  {
    var quotation = prompt('Por favor, ingrese el valor de la cotización en USD sin impuestos');
    if (quotation != '')
    {
      $('#5_' + id).text(quotation + ' USD');
      qt = quotation;
    }
  }
  $('#ld_' + id).show();
  taskRunning = true;
  $.post( "/upgradepurchase", { id:id, status:rq, quotation:qt, "_token": $('#tk').text(), })
  .done(function( data ) {
    if(data[7]) goToPurchaseOrderPage(data[7]);
    if(data[6])
    {
      if(rq!=6)
      {
        if(orders_status[id] == 1)
        {
          for (const [key, value] of Object.entries(orders))
          {
            if((orders[key] == orders[id]) && (key != id) && data[6])
            {
              $.post( "/upgradepurchase", { id:key, status:6, quotation:qt, "_token": $('#tk').text(), })
              .done(function( data ) {
                orders_status[key] = 6;
                $('#4_' + key + ' div').html(data[0]);
                $('#4_' + key + ' div').attr("data-content", 'Cambiar a ' + data[3]);
                $('#4_' + key + ' div').popover("show");
                $('#4_' + key + ' div').removeClass();
                $('#4_' + key + ' div').addClass('btn btn-sm btn-dark');
              })
              .fail(function( data ) {
                alert( "Hubo un problema. No pudimos actualizar correctamente el estado de la órden. Por favor revise todas las cotizaciones de la misma luego de actualizar la página." + JSON.stringify(data, null, 4)  );
              });
            }
          }
        }
        orders_status[id] = data[2];
        $('#4_' + id + ' div').html(data[0]);
        $('#4_' + id + ' div').attr("data-content", 'Cambiar a ' + data[3]);
        $('#4_' + id + ' div').popover("show");
        $('#4_' + id + ' div').removeClass();
        $('#4_' + id + ' div').addClass(data[1]);
        if(data[2] == 3)
        {
          $('#6_' + id + ' img').attr("onclick", "goToPurchaseOrder('" + data[4] + "')");
          $('#6_' + id + ' img').attr("title", "Ver " + data[5]);
          $('#6_' + id + ' img').removeClass("disabled_table_icon");
          $('#6_' + id + ' img').addClass("table_icon");
        }
      }else{
        delete orders_status[id];
        $('#4_' + id + ' div').parent().parent().remove();
      }
    }else{
      alert('Acceso no permitido. Por favor solicite el permiso a un administrador del sistema.');
    }
    $('#ld_' + id).hide();
    taskRunning = false;
  })
  .fail(function( data ) {
    alert( "Hubo un problema. No pudimos actualizar el estado de la órden." );
    $('#ld_' + id).hide();
    taskRunning = false;
  });
}

function definitiveDeletePurchase_order(element){
  var id = element.parentNode.id.replace('8_','');;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar definitivamente la órden " + $(row.cells[0]).text() + '? Esta acción no se puede deshacer')){
    table.removeChild(row);
    $.post( "/definitivedeletepurchase", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar la órden");
    });
  }
}

function goToPurchaseOrder(A){
  var win = window.open(window.location.origin + '/purchaseOrder/pdf/' + A , '_blank');
  if (!win) {
      alert('Por favor permita las ventanas emergentes para este sitio.');
  }
}

function goToPurchaseOrderPage(A){
  var win = window.open(window.location.origin + '/purchase_order/' + A , '_blank');
  if (!win) {
      alert('Por favor permita las ventanas emergentes para este sitio.');
  }
}
