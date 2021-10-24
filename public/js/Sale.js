$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodySales tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  $('#add_project').click(function(){
    var newProject = $( ".project" ).clone().appendTo( "#sales" );
    $( newProject ).removeClass('project');
    $( newProject ).removeClass('d-none');
  });
  $('#add_subset').click(function(){
    var newSubset = $( ".subset-block" ).clone().appendTo( "#sales" );
    $( newSubset ).removeClass('subset-block');
    $( newSubset ).removeClass('d-none');
  });
  $('#add_element').click(function(){
    var newElement = $( ".element-block" ).clone().appendTo( "#sales" );
    $( newElement ).removeClass('element-block');
    $( newElement ).removeClass('d-none');
  });

  $('.bill_number').hide();
  $('.order_number').hide();
  $('.scheduled_delivery_date').hide();
  $('.retentions').hide();
  $('.perceptions').hide();
  $('.requested_delivery_date').hide();
  $('.currencies').hide();

  statusChange($('#status_selector'));
  $('.preloaded_projects').each(function () {
    saleCompleteSubsetsSelector(this,$(this).attr("subset"));
  })

});

function editSale(id){
  location.href = "/editsale/" + id;
}

function statusChange(status) {
  var status = $(status).val();
  switch(parseInt(status))
  {
    case 1:
      $('.bill_number').hide();
      $('.order_number').hide();
      $('.scheduled_delivery_date').hide();
      $('.retentions').hide();
      $('.perceptions').hide();
      $('.requested_delivery_date').hide();
      $('.currencies').hide();
      $('.quoted_date').hide();
      $('.purchase_order_reception_date').hide();
      $('.ready_to_deliver_date').hide();
      $('.delivered_date').hide();
    break;
    case 2:
      $('.bill_number').hide();
      $('.order_number').hide();
      $('.scheduled_delivery_date').show();
      $('.retentions').hide();
      $('.perceptions').hide();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').hide();
      $('.ready_to_deliver_date').hide();
      $('.delivered_date').hide();
    break;
    case 3:
      $('.bill_number').hide();
      $('.order_number').show();
      $('.scheduled_delivery_date').show();
      $('.retentions').hide();
      $('.perceptions').hide();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').show();
      $('.ready_to_deliver_date').hide();
      $('.delivered_date').hide();
    break;
    case 4:
      $('.bill_number').show();
      $('.order_number').show();
      $('.scheduled_delivery_date').show();
      $('.retentions').show();
      $('.perceptions').show();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').show();
      $('.ready_to_deliver_date').hide();
      $('.delivered_date').hide();
    break;
    case 5:
      $('.bill_number').show();
      $('.order_number').show();
      $('.scheduled_delivery_date').show();
      $('.retentions').show();
      $('.perceptions').show();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').show();
      $('.ready_to_deliver_date').show();
      $('.delivered_date').hide();
    break;
    case 6:
      $('.bill_number').show();
      $('.order_number').show();
      $('.scheduled_delivery_date').show();
      $('.retentions').show();
      $('.perceptions').show();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').show();
      $('.ready_to_deliver_date').show();
      $('.delivered_date').show();
    break;
    case 7:
      $('.bill_number').show();
      $('.order_number').show();
      $('.scheduled_delivery_date').show();
      $('.retentions').show();
      $('.perceptions').show();
      $('.requested_delivery_date').show();
      $('.currencies').show();
      $('.quoted_date').show();
      $('.purchase_order_reception_date').show();
      $('.ready_to_deliver_date').show();
      $('.delivered_date').show();
    break;
  }
}

//ELEMENT AUTOCOMPLETE START terminar

function sale_element_keyup(el, e) {
       if((e.which != 40) && (e.which != 38) && (e.which != 13) && (e.which != 27)){
         var query = $(el).val();
         if(query != '')
         {
          var _token = $('input[name="_token"]').val();
          $.ajax({
           url: saleElementsUrl,
           method:"POST",
           data:{query:query, _token:_token},
           success:function(data){
            $(el).parent().children('.saleElementIdList').fadeIn();
            $(el).parent().children('.saleElementIdList').html(data);
            saleLi = $('.saleElementSelectable');
            saleLiSelected = false;
           }
          });
        }else{
          $(el).parent().children('.SaleElementIdList').fadeOut();
        }
      };
   };

   $(document).on('click', '.saleSearchElementLi', function(){
       $(this).parent().parent().parent().children('.sale_element_input').val(parseInt($(this).attr('id').replace("saleElement", "")));
       $(this).parent().parent().parent().children('.sale_element_input').hide();
       $(this).parent().parent().parent().children('.sale_element_input_selected').children().text($(this).text());
       $(this).parent().parent().parent().children('.sale_element_input_selected').show();
       $(this).parent().parent().parent().children('.sale_element_input_cancel_button').show();
       $(this).parent().parent().fadeOut();
       $(this).parent().remove();
       //$(this).parent().parent().submit();
   });

   $(window).keydown(function(e){
     //if($('.sale_element_input').is(":focus"))
     {
       if(e.which === 40){
           if(saleLiSelected){
               saleLiSelected.removeClass('saleElementSelected');
               next = saleLiSelected.next();
               if(next.length > 0){
                   saleLiSelected = next.addClass('saleElementSelected');
               }else{
                   saleLiSelected = saleLi.eq(0).addClass('saleElementSelected');
               }
           }else{
               saleLiSelected = saleLi.eq(0).addClass('saleElementSelected');
           }
       }else if(e.which === 38){
           if(saleLiSelected){
               saleLiSelected.removeClass('saleElementSelected');
               next = saleLiSelected.prev();
               if(next.length > 0){
                   saleLiSelected = next.addClass('saleElementSelected');
               }else{
                   saleLiSelected = saleLi.last().addClass('saleElementSelected');
               }
           }else{
               saleLiSelected = saleLi.last().addClass('saleElementSelected');
           }
       }else if(e.which === 13){
           if(saleLiSelected){
             saleLiSelected.parent().parent().parent().children('.sale_element_input').val(parseInt(saleLiSelected.attr('id').replace("saleElement", "")));
             saleLiSelected.parent().parent().parent().children('.sale_element_input').hide();
             saleLiSelected.parent().parent().parent().children('.sale_element_input_selected').children().text(saleLiSelected.text());
             saleLiSelected.parent().parent().parent().children('.sale_element_input_selected').show();
             saleLiSelected.parent().parent().parent().children('.sale_element_input_cancel_button').show();
             $('.saleElementIdList').fadeOut();
             $('.saleElementIdList').children().remove();
             e.preventDefault();
             //$('#elementSearchForm').submit();
           }
       }else if(e.which === 27){
             $('.saleElementIdList').fadeOut();
             $('.saleElementIdList').children().remove();
       }
     }

       if($('.saleElementSelected').position())
       {
         var $container = $('.saleElementSelected').parent(),
         $scrollTo = $('.saleElementSelected');
         $container.scrollTop(
           $scrollTo.offset().top - $container.offset().top + $container.scrollTop()-100
         );
       }

   });

function cancelElementSelection(element) {
  $(element).parent().parent().children('.sale_element_input').val('');
  $(element).parent().parent().children('.sale_element_input').show();
  $(element).parent().parent().children('.sale_element_input').focus();
  $(element).parent().parent().children('.sale_element_input_selected').hide();
  $(element).parent().parent().children('.sale_element_input_cancel_button').hide();
}

//ELEMENT AUTOCOMPLETE END

function removeItem(item) {
  if(confirm("Seguro desea remover el item de la venta?"))
  {
    $(item).parent().parent().remove();
  }
}


function saleCompleteSubsetsSelector(id, subset = null) {
  $(id).parent().parent().children(".subset_ld").show();
  $.post( "/completesubsetsselector", { id: id.value, "_token": $('#tk').text(), })
  .done(function( data ) {
    $(id).parent().parent().children(".subset-container").children(".subset").html(data);
    $(id).parent().parent().children(".subset_ld").hide();
    if(subset)
    {
      $(id).parent().parent().children(".subset-container").children(".subset").val(subset).change();
    }
  })
  .fail(function( data ) {
    alert( "Error al buscar subconjuntos del proyecto." + JSON.stringify(data, null, 4) );
    $(id).parent().parent().children(".subset_ld").hide();
  });
}
