$(document).ready(function(){
    if (typeof order_types !== 'undefined')
    {
      if(order_types[$("#order_type_id").val()][0]){$('#d_ext').show();$('label[for="d_ext"]').show();}else{$('#d_ext').hide();$('label[for="d_ext"]').hide();$('#d_ext').val(0);}
      if(order_types[$("#order_type_id").val()][1]){$('#d_int').show();$('label[for="d_int"]').show();}else{$('#d_int').hide();$('label[for="d_int"]').hide();$('#d_int').val(0);}
      if(order_types[$("#order_type_id").val()][2]){$('#side_a').show();$('label[for="side_a"]').show();}else{$('#side_a').hide();$('label[for="side_a"]').hide();$('#side_a').val(0);}
      if(order_types[$("#order_type_id").val()][3]){$('#side_b').show();$('label[for="side_b"]').show();}else{$('#side_b').hide();$('label[for="side_b"]').hide();$('#side_b').val(0);}
      if(order_types[$("#order_type_id").val()][4]){$('#large').show();$('label[for="large"]').show();}else{$('#large').hide();$('label[for="large"]').hide();$('#large').val(0);}
      if(order_types[$("#order_type_id").val()][5]){$('#width').show();$('label[for="width"]').show();}else{$('#width').hide();$('label[for="width"]').hide();$('#width').val(0);}
      if(order_types[$("#order_type_id").val()][6]){$('#thickness').show();$('label[for="thickness"]').show();}else{$('#thickness').hide();$('label[for="thickness"]').hide();$('#thickness').val(0);}
      $("#order_type_id").change(function(){
          if(order_types[$(this).children("option:selected").val()][0]){$('#d_ext').show();$('label[for="d_ext"]').show();}else{$('#d_ext').hide();$('label[for="d_ext"]').hide();$('#d_ext').val(0);}
          if(order_types[$(this).children("option:selected").val()][1]){$('#d_int').show();$('label[for="d_int"]').show();}else{$('#d_int').hide();$('label[for="d_int"]').hide();$('#d_int').val(0);}
          if(order_types[$(this).children("option:selected").val()][2]){$('#side_a').show();$('label[for="side_a"]').show();}else{$('#side_a').hide();$('label[for="side_a"]').hide();$('#side_a').val(0);}
          if(order_types[$(this).children("option:selected").val()][3]){$('#side_b').show();$('label[for="side_b"]').show();}else{$('#side_b').hide();$('label[for="side_b"]').hide();$('#side_b').val(0);}
          if(order_types[$(this).children("option:selected").val()][4]){$('#large').show();$('label[for="large"]').show();}else{$('#large').hide();$('label[for="large"]').hide();$('#large').val(0);}
          if(order_types[$(this).children("option:selected").val()][5]){$('#width').show();$('label[for="width"]').show();}else{$('#width').hide();$('label[for="width"]').hide();$('#width').val(0);}
          if(order_types[$(this).children("option:selected").val()][6]){$('#thickness').show();$('label[for="thickness"]').show();}else{$('#thickness').hide();$('label[for="thickness"]').hide();$('#thickness').val(0);}
      });
      $(".btn-submit").click(function(e){
              e.preventDefault();
              var order_type_id = $("#order_type_id").val();
              var material_id = $("#material_id").val();
              var d_ext = $('#d_ext').val();
              var d_int = $('#d_int').val();
              var side_a = $('#side_a').val();
              var side_b = $('#side_b').val();
              var large = $('#large').val();
              var width = $('#width').val();
              var thickness = $('#thickness').val();
              var price = $('#price').val();
              $.ajax({
                 url:url,
                 type:'POST',
                 data:{material_id:material_id,order_type_id:order_type_id,d_ext:d_ext, d_int:d_int, side_a:side_a, side_b:side_b, large:large, width:width, thickness:thickness, price:price},
                 success:function(data){
                   if(data.success === true || data.success === false)
                   {
                     if(!data.success)
                     {
                      var conf = confirm('El precio que insertó al elemento difiere mucho de otros ya cargados. Desea continuar de todos modos?');
                      if (conf == true)
                      {
                        $('#main-form').submit();
                      }
                    } else {
                      $('#main-form').submit();
                    }
                  }else{
                    alert(data.success);
                  }
                 }
              });
        });
      };
    });

function editPrice(id){
  location.href = "/editmaterialprice/" + id;
}

function deletePrice(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar el precio?")){
    table.removeChild(row);
    $.post( "/deletematerialprice", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar el precio" + JSON.stringify(data, null, 4) );
    });
  }
}
