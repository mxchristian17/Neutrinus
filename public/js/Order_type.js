$(document).ready(function(){
  $("#tableSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbodyOrder_types tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

    if($("#formula").length){
      $("#formula").val('');
    }
  if(typeof(startingFormula)=='undefined'){
    formulaGenerator('0');
  }else{
    formulaGenerator('');
  }
  startEnableDisable('d_ext');startEnableDisable('d_int');startEnableDisable('side_a');startEnableDisable('side_b');startEnableDisable('large');startEnableDisable('width');startEnableDisable('thickness');
});
if(typeof(formula)=='undefined'){var formula = [];}
var enabledFormItems = [];
function formulaGenerator(id){
  if ((formula.length == 1) && (formula[0] == 0)){formula = [];}
  var prevVal = formula[formula.length-1]; var warningNotif = 'Sintaxis de operación incorrecta'; switch(id){ case 'x2':if(checkSymbolEnable(prevVal)){formula.push('^2');}else{alert(warningNotif)};break; case 'x3':if(checkSymbolEnable(prevVal)){formula.push('^3');}else{alert(warningNotif)};break; case 'sqrx':if(checkVarEnable(prevVal)){formula.push('RAIZ(');}else{alert(warningNotif)};break; case 'flarge':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('Largo');}else{alert(warningNotif)};break; case 'fwidth':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('Ancho');}else{alert(warningNotif)};break; case 'fthickness':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('Espesor');}else{alert(warningNotif)};break; case 'fd_ext':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('Øext');}else{alert(warningNotif)};break; case 'fd_int':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('Øint');}else{alert(warningNotif)};break; case 'fside_a':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('LadoA');}else{alert(warningNotif)};break; case 'fside_b':if(!enabledFormItems[id]){ return;}if(checkVarEnable(prevVal)){formula.push('LadoB');}else{alert(warningNotif)};break; case '1':if(checkNumEnable(prevVal)){formula.push('1');}else{alert(warningNotif)};break; case '2':if(checkNumEnable(prevVal)){formula.push('2');}else{alert(warningNotif)};break; case '3':if(checkNumEnable(prevVal)){formula.push('3');}else{alert(warningNotif)};break; case '4':if(checkNumEnable(prevVal)){formula.push('4');}else{alert(warningNotif)};break; case '5':if(checkNumEnable(prevVal)){formula.push('5');}else{alert(warningNotif)};break; case '6':if(checkNumEnable(prevVal)){formula.push('6');}else{alert(warningNotif)};break; case '7':if(checkNumEnable(prevVal)){formula.push('7');}else{alert(warningNotif)};break; case '8':if(checkNumEnable(prevVal)){formula.push('8');}else{alert(warningNotif)};break; case '9':if(checkNumEnable(prevVal)){formula.push('9');}else{alert(warningNotif)};break; case '0':if(checkNumEnable(prevVal)){formula.push('0');}else{alert(warningNotif)};break; case 'pi':if(checkVarEnable(prevVal)){formula.push('π');}else{alert(warningNotif)};break; case 'coma': if(checkComaEnable(prevVal)){formula.push(',');}else{alert(warningNotif)};break; case 'add':if(checkSymbolEnable(prevVal)){formula.push('+');}else{alert(warningNotif)};break; case 'subtract':if(checkSymbolEnable(prevVal)){formula.push('-');}else{alert(warningNotif)};break; case 'multiply':if(checkSymbolEnable(prevVal)){formula.push('*');}else{alert(warningNotif)};break; case 'divide':if(checkSymbolEnable(prevVal)){formula.push('/');}else{alert(warningNotif)};break; case 'openparentheses':if(checkVarEnable(prevVal)){formula.push('('); break;}else{alert(warningNotif)};break; case 'closeparentheses':if(checkCloseParenthesesEnable(prevVal)){if((countCoincidences(formula, '(') + countCoincidences(formula, 'RAIZ(')) > countCoincidences(formula, ')')){formula.push(')');}else{alert(warningNotif)};}else{alert(warningNotif)};break; case 'c': formula = []; break; case 'backspace': formula.pop(); break; };if(formula.length == 0){formula.push('0');};$('#formula').val(formula.join(''));$('#formulaSend').val(formula.join('$'));
}
function checkSymbolEnable(prevVal){
  switch(prevVal){ case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': case '0': case 'π': case ')': case 'Øext': case 'Øint': case 'LadoA': case 'LadoB': case 'Largo': case 'Ancho': case 'Espesor': case '^2': case '^3': return true; break; default: return false; break; }
}
function checkComaEnable(prevVal){
switch(prevVal){case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': case '0': return true; break; default: return false; break; }
}
function checkCloseParenthesesEnable(prevVal){
  switch(prevVal){ case '(': case 'RAIZ(': return false; break; default: return true; break; }
}
function checkVarEnable(prevVal){
  if(formula.length == 0){ return true; }
  switch(prevVal){ case '(': case 'RAIZ(': case '+': case '-': case '*': case '/': return true; break; default: return false; break; }
}
function checkNumEnable(prevVal){
  if(formula.length == 0){ return true; }
  switch(prevVal){ case 'π': case ')': case 'Øext': case 'Øint': case 'LadoA': case 'LadoB': case 'Largo': case 'Ancho': case 'Espesor': return false; break; default: return true; break; }
}
function countCoincidences(array,searchElement){
  var count = 0;
  for(var i = 0; i < array.length; ++i){
      if(array[i] == searchElement)
          count++;
  }
  return count;
}
function enableDisable(v){
  if($('#' + v).is(':checked')){
    $('#f'+ v).addClass( "td-formulaGenerator-disabled" );
    enabledFormItems['f' + v] = 0;
  }else{
    $('#f'+ v).removeClass( "td-formulaGenerator-disabled" );
    enabledFormItems['f' + v] = 1;
  }
}
function startEnableDisable(v){
  if(!$('#' + v).is(':checked')){
    $('#f'+ v).addClass( "td-formulaGenerator-disabled" );
    enabledFormItems['f' + v] = 0;
  }else{
    $('#f'+ v).removeClass( "td-formulaGenerator-disabled" );
    enabledFormItems['f' + v] = 1;
  }
}
function checkSubmit(e){
  if ($('#name').val().length ==0){
    alert('El nombre no puede estar vacio');
    e.preventDefault();
    return false;
  }
  if ((countCoincidences(formula, '(') + countCoincidences(formula, 'RAIZ(')) != countCoincidences(formula, ')')){
    alert('Hay parentesis sin cerrar en la fórmula de volumen');
    e.preventDefault();
    return false;
  }
}

function editOrder_type(id){
  location.href = "/editordertype/" + id;
}

function deleteOrder_type(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar el elemento " + $(row.cells[0]).text())){
    table.removeChild(row);
    $.post( "/deleteorder_type", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar el tipo de pedido" + JSON.stringify(data, null, 4) );
    });
  }
}
function definitiveDeleteOrder_type(element){
  var id = element.parentNode.id;
  var table = element.parentNode.parentNode.parentNode;
  var row = element.parentNode.parentNode;
  if(window.confirm("Seguro desea eliminar definitivamente el elemento " + $(row.cells[0]).text() + '? Esta acción no se puede deshacer')){
    table.removeChild(row);
    $.post( "/definitivedeleteordertype", { id: id, "_token": $('#tk').text(), })
    .fail(function( data ) {
      alert( "Error al eliminar el tipo de pedido" + JSON.stringify(data, null, 4) );
    });
  }
}
