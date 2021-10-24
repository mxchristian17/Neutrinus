$(document).ready(function(){
  $('.permission').change(function() {
      //if(this.checked) {
      //    $(this).prop("checked", returnVal);
      //}
      //alert(this.checked);
      if(this.checked) { var state = 1; }else{ var state = 0; }
      var cid = $(this).prop("name");
      var id = $(this).prop("id");
      $.post( "/editUserPermission", { uid: uid, "_token": $('#tk').text(), cid: cid, state: state })
      .done(function( data ) {
        alert($("[for=" + id + "]").text() + " " + data);
      })
      .fail(function( data ) {
        alert( "Error al cambiar el permiso" + JSON.stringify(data, null, 4) );
      });
  });
  $('.authLevel').change(function() {
      if(this.checked) { var rid = $(this).prop("value"); }else{ return null; }
      var id = $(this).prop("id");
      $.post( "/edituserauthlevel", { uid: uid, "_token": $('#tk').text(), rid: rid })
      .done(function( data ) {
        alert(uname + data + $("[for=" + id + "]").text());
      })
      .fail(function( data ) {
        alert( "Error al cambiar el permiso" + JSON.stringify(data, null, 4) );
      });
  });
  $('.status').change(function() {
      var rid = this.value;
      var id = $(this).prop("id");
      $.post( "/edituserstatus", { uid: uid, "_token": $('#tk').text(), rid: rid })
      .done(function( data ) {
        alert(uname + data + $("#" + id + " option:selected").text());
      })
      .fail(function( data ) {
        alert( "Error al cambiar el estado" + JSON.stringify(data, null, 4) );
      });
  });
});
function toggleTemplates() {
  $('.plantilla').toggle('fast');
}
function changeStartPage(v) {
  var val = 0;
  if (v.checked) val = 1;
  $.post( "/updateuserpreferences", { preference: 1, "_token": $('#tk').text(), val: val })
  .done(function( data ) {
    ;
  })
  .fail(function( data ) {
    alert( "Error al cambiar el estado" + JSON.stringify(data, null, 4) );
  });
}

function changeShowGeneralElementSearch(v) {
  var val = 0;
  if(v.checked) val = 1;
  $.post( "/updateuserpreferences", { preference: 2, "_token": $('#tk').text(), val: val })
  .done(function( data ) {
    if (v.checked)
    {
      if (document.getElementById('elementSearchForm')) {
        $('#elementSearchForm').show();
      }else{
        location.reload();
      }
    }else{
      $('#elementSearchForm').hide();
    }
  })
  .fail(function( data ) {
    alert( "Error al cambiar el estado" );
  });
}

function confirmUserDelete(e) {
  if(!confirm('Seguro desea eliminar al usuario del sistema? Al hacer esto le quitará todos los accesos y no podrá ser recuperado.'))
  {
    e.preventDefault();
  }
}
