$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyClients tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function addContact()
{
  var newContact = $( ".contact" ).clone().appendTo( "#contacts" );
  $( newContact ).removeClass('contact');
  $('.contact').children().val('');
  $( newContact ).find('.addContact').remove();
  $( newContact).children().append('<button class="btn btn-primary" onclick="removeContact(this)" title="Añadir otro contacto" type="button">-</button>');
}

function removeContact(element)
{
  $( element ).parent().parent().remove();
}

function editClient(id){
  location.href = "/editclient/" + id;
}
