$(document).ready(function(){
 var li = $('.selectable');
 var liSelected;
 $('#supplier').focus();
 $('#supplier').keyup(function(e){
        if((e.which != 40) && (e.which != 38) && (e.which != 13) && (e.which != 27)){
          var query = $(this).val();
          if(query != '')
          {
           var _token = $('input[name="_token"]').val();
           $.ajax({
            url: url,
            method:"POST",
            data:{query:query, _token:_token},
            success:function(data){
             $('#supplierIdList').fadeIn();
             $('#supplierIdList').html(data);
             li = $('.selectable');
             liSelected = false;
            }
           });
         }else{
           $('#supplierIdList').fadeOut();
         }
       }
    });

    $(document).on('click', 'li', function(){
        $('#supplier').val($(this).text());
        selectedSupplier = $(this).attr('val');
        selectedSupplierName = $(this).text();
        addSupplierToShoppingCart();
        $('#supplier').val('');
        $('#supplierIdList').fadeOut();
        $('#supplierSearchForm').submit();
    });

    $(window).keydown(function(e){
        if(e.which === 40){
            if(liSelected){
                liSelected.removeClass('selected');
                next = liSelected.next();
                if(next.length > 0){
                    liSelected = next.addClass('selected');
                }else{
                    liSelected = li.eq(0).addClass('selected');
                }
            }else{
                liSelected = li.eq(0).addClass('selected');
            }
        }else if(e.which === 38){
            if(liSelected){
                liSelected.removeClass('selected');
                next = liSelected.prev();
                if(next.length > 0){
                    liSelected = next.addClass('selected');
                }else{
                    liSelected = li.last().addClass('selected');
                }
            }else{
                liSelected = li.last().addClass('selected');
            }
        }else if(e.which === 13){
            if(liSelected){
              $('#supplier').val(liSelected.text());
              selectedSupplier = liSelected.attr('val');
              selectedSupplierName = liSelected.text();
              addSupplierToShoppingCart();
              $('#supplier').val('');
              $('#supplierIdList').fadeOut();
              e.preventDefault();
              $('#supplierSearchForm').submit();
            }
        }else if(e.which === 27){
              $('#supplierIdList').fadeOut();
        }

        if($('.selected').position())
        {
          var $container = $('#supplierSelector'),
          $scrollTo = $('.selected');
          $container.scrollTop(
            $scrollTo.offset().top - $container.offset().top + $container.scrollTop()-100
          );
        }

    });
});

function shoppingCartItemUpdate(id, val, rowId)
{
  $.post( "/updatefromshoppingcart", { id:id, qty: parseInt(val), rowId:rowId, "_token": $('#tk').text(), })
  .done(function( data ) {
    if(parseInt(data[1])>0)
    {
      $('.shoppingCartQty').show();
      $('.shoppingCartQty').text('(' + data[1] + ')');
    }else{
      $('.shoppingCartQty').hide();
    }
    $('#total_subtotal').text('$' + data[2]);
    $('#total_tax').text('$' + data[3]);
    $('#total_total').text('$' + data[4]);
    $('#st_' + rowId).text('$' + data[5]);
    $('#t_' + rowId).text('$' + data[6]);
    $('#tt_' + rowId).text('$' + data[7]);
    $('#q_' + rowId).val(data[8]);
  })
  .fail(function( data ) {
    alert( "Hubo un problema. No pudimos actualizar el item del carro de compras." );
  });
}

function removeFromShoppingCart(id, rowId, name){
  var answer = confirm ("Seguro desea eliminar " + name + " del carro?");
  if(answer == true){
    $.post( "/removefromshoppingcart", { id: id, rowId:rowId, "_token": $('#tk').text(), })
    .done(function( data ) {
      if(parseInt(data[1])>0)
      {
        $('.shoppingCartQty').show();
        $('.shoppingCartQty').text('(' + data[1] + ')');
      }else{
        $('.shoppingCartQty').hide();
      }
      $('#r_' + rowId).remove();
      $('#total_subtotal').text('$' + data[2]);
      $('#total_tax').text('$' + data[3]);
      $('#total_total').text('$' + data[4]);
    })
    .fail(function( data ) {
      alert( "Hubo un problema. No pudimos remover el item del carro de compras." );
    });
  }
  return;
}

function addSupplierToShoppingCart()
{
  if($('#supplier_' + selectedSupplier).length == 0) {
    $.post( "/addsuppliertoshoppingcart", { id: selectedSupplier, "_token": $('#tk').text(), })
    .done(function( data ) {
      $('#selectedSuppliers').append('<div class="d-inline-flex btn-sm btn-success mr-2 mb-1" id="supplier_' + selectedSupplier + '"><span class="d-inline-flex btn-sm text-white">' + selectedSupplierName + ' </span><span class="d-inline-flex btn btn-sm text-white" onclick="removeSupplierFromShoppingCart(\'' + selectedSupplier + '\')">X</span></div>');
      document.getElementById('alertBox').innerHTML='<b>' + data + '</b>';
      $('#alertBox').fadeIn('slow');
      setTimeout(function() {$('#alertBox').fadeOut('slow');},5000);
    })
    .fail(function( data ) {
      alert( "Hubo un problema. No pudimos agregar el proveedor al carro de compras." );
    });
  }
}

function removeSupplierFromShoppingCart(id)
{
  $.post( "/removesupplierfromshoppingcart", { id: id, "_token": $('#tk').text(), })
  .done(function( data ) {
    $('#supplier_' + id).remove();
    document.getElementById('alertBox').innerHTML='<b>' + data + '</b>';
    $('#alertBox').fadeIn('slow');
    setTimeout(function() {$('#alertBox').fadeOut('slow');},5000);
  })
  .fail(function( data ) {
    alert( "Hubo un problema. No pudimos remover el proveedor del carro de compras." );
  });
}
