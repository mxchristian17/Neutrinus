<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('startserver', 'LoginadminController@startserver');
Route::post('sendLoginAdmin', 'LoginadminController@login');

Route::get('neutrinus/error/400', 'ErrorController@badRequest')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('neutrinus/error/405', 'ErrorController@notAllowed')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('/', 'HomeController@index')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('projects/{showAll?}', 'ProjectController@showProjects')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('project/{id}/{showAll?}', 'ProjectController@showProject')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createproject', 'ProjectController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storeproject', 'ProjectController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editproject/{id}', 'ProjectController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateproject/{id}', 'ProjectController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateproject');
Route::post('/deleteproject', 'ProjectController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeleteproject', 'ProjectController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('projectstats/{id}', 'ProjectController@showProjectStats')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/completesubsetsselector', 'ProjectController@completeSubsetsSelector')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('/createsubset/{project}', 'SubsetController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storesubset', 'SubsetController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editsubset/{subset}', 'SubsetController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updatesubset/{id}', 'SubsetController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatesubset');
Route::get('/deletesubset/{id}', 'SubsetController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/definitivedeletesubset/{id}', 'SubsetController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/subsetcopyto', 'SubsetController@copyTo')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('subsetcopyto');

Route::get('projectelement/{id}/{showAll?}', 'ProjectelementController@showProjectelement')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createprojectelement/{project}/{subset}', 'ProjectelementController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('createProjectElement');
Route::post('/storeprojectelement', 'ProjectelementController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editprojectelement/{id}', 'ProjectelementController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('editProjectElement');
Route::post('/updateprojectelement/{id}', 'ProjectelementController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateprojectelement');
Route::post('/deleteprojectelement', 'ProjectelementController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeleteprojectelement', 'ProjectelementController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/elementcopyto', 'ProjectelementController@copyTo')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('elementcopyto');

Route::get('elements/{stateVisibility?}', 'ElementController@showElements')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('element/{id}/{showAll?}', 'ElementController@showElement')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createelement', 'ElementController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storeelement', 'ElementController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editelement/{id}', 'ElementController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateelement/{id}', 'ElementController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateelement');
Route::post('/deleteelement', 'ElementController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeleteelement', 'ElementController@deepDelete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/autocompleteelement/fetch', 'ElementController@fetch')->name('autocompleteelement.fetch')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/autocompleteelement/salefetch', 'ElementController@saleFetch')->name('autocompleteelement.salefetch')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/searchelement', 'ElementController@searchElement')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('elementSearch');
Route::post('/storesuppliercode', 'ElementController@storeSupplierCode')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/deletesuppliercode/{id}/{_token}', 'ElementController@deepDeleteSupplierCode')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('suppliers/{showAll?}', 'SupplierController@showSuppliers')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('supplier/{id}', 'SupplierController@showSupplier')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('createsupplier', 'SupplierController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('storesupplier', 'SupplierController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('editsupplier/{id}', 'SupplierController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('updatesupplier/{id}', 'SupplierController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatesupplier');
Route::post('/autocompletesupplier/fetch', 'SupplierController@fetch')->name('autocompletesupplier.fetch')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('clients/{showAll?}', 'ClientController@showClients')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('client/{id}', 'ClientController@showClient')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('createclient', 'ClientController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('editclient/{id}', 'ClientController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('storeclient', 'ClientController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('updateclient/{id}', 'ClientController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateclient');

Route::get('materialprices', 'Material_priceController@showPrices')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('creatematerialprice', 'Material_priceController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storematerialprice', 'Material_priceController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editmaterialprice/{id}', 'Material_priceController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updatematerialprice/{id}', 'Material_priceController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatematerialprice');
Route::post('/deletematerialprice', 'Material_priceController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('checklogicprice', 'Material_priceController@checkLogicPrice')->name('checklogicprice')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('materials/{showAll?}', 'MaterialController@showMaterials')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/creatematerial', 'MaterialController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storematerial', 'MaterialController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editmaterial/{id}', 'MaterialController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updatematerial/{id}', 'MaterialController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatematerial');
Route::post('/deletematerial', 'MaterialController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeletematerial', 'MaterialController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('ordertypes/{showAll?}', 'Order_typeController@showOrder_types')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createorder_type', 'Order_typeController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storeorder_type', 'Order_typeController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editordertype/{id}', 'Order_typeController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateordertype/{id}', 'Order_typeController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateordertype');
Route::post('/deleteorder_type', 'Order_typeController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeleteordertype', 'Order_typeController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('user/{id}', 'UserController@showUser')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/edituser/{id}', 'UserController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateuser/{id}', 'UserController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateuser');
Route::get('/preferences', 'UserController@editUserPreferences')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateuserpreferences', 'UserController@updateUserPreferences')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/editUserPermission', 'UserController@editPermission')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/edituserauthlevel', 'UserController@editUserAuthLevel')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/edituserstatus', 'UserController@editUserStatus')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/userPermissionManager', 'UserController@showUserPermissionManager')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/setpermissiontemplate/{user_id}/{template_id}', 'UserController@setPermissionTemplate')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('setpermissiontemplate');
Route::post('/editUserAtCharge', 'UserController@editUserAtCharge')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/deleteuser/{id}', 'UserController@deleteUser')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('/ext_f_1/{id}/{title}', 'ProjectelementController@showExt_f_1')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/element/ext_f_1/{id}/{title}', 'ElementController@showExt_f_1')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('/help/elements', 'HelpController@elements')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/help/order_types', 'HelpController@order_types')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/help/operations', 'HelpController@operations')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('/editoperation/{id}', 'OperationController@editOperation')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateoperation/{id}', 'OperationController@updateOperation')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateoperation');
Route::get('/deleteoperation/{id}/{_token}', 'OperationController@deleteOperation')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/definitivedeleteoperation/{id}/{_token}', 'OperationController@deleteForEverOperation')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('operation_names/{showAll?}', 'OperationController@showOperation_names')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createoperation_name', 'OperationController@createOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storeoperation_name', 'OperationController@storeOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editoperation_name/{id}', 'OperationController@editOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updateoperation_name/{id}', 'OperationController@updateOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updateoperationname');
Route::post('/deleteoperation_name', 'OperationController@deleteOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeleteoperation_name', 'OperationController@deleteForEverOperation_name')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::post('/storeoperation', 'OperationController@storeOperation')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('purchase_orders/{supplier?}', 'PurchaseController@showPurchaseOrders')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('purchase_order/{id}', 'PurchaseController@showPurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('quotationrequest', 'PurchaseController@storePurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('generatepurchaseorder/{status}', 'PurchaseController@storePurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editpurchase_order/{id}', 'PurchaseController@editPurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updatepurchase_order/{id}', 'PurchaseController@updatePurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatepurchaseorder');
Route::post('/deletePurchase_order', 'PurchaseController@deletePurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeletepurchase_order', 'PurchaseController@deleteForEverPurchaseOrder')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/upgradepurchase', 'PurchaseController@upgradePurchase')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeletepurchase', 'PurchaseController@deleteForEverPurchase')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/purchaseOrder/pdf/{id}', 'PurchaseController@showPdf')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::get('shoppingcart', 'PurchaseController@showShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('destroycart', 'PurchaseController@destroyCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('addtoshoppingcart', 'PurchaseController@addToShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('updatefromshoppingcart', 'PurchaseController@updateFromShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('removefromshoppingcart', 'PurchaseController@removeFromShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('addsuppliertoshoppingcart', 'PurchaseController@addSupplierToShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('removesupplierfromshoppingcart', 'PurchaseController@removeSupplierFromShoppingCart')->middleware('auth', 'chatdata', 'reminders', 'tasks');

Route::post('newreminder', 'ReminderController@newReminder')->middleware('auth');
Route::post('checkreminders', 'ReminderController@checkNewReminders')->middleware('auth');
Route::post('cancelreminder', 'ReminderController@cancelReminder')->middleware('auth');
Route::post('cancelreminderforever', 'ReminderController@cancelReminderForEver')->middleware('auth');
Route::post('postponereminder', 'ReminderController@postponeReminder')->middleware('auth');

Route::post('newtask', 'TaskController@newTask')->middleware('auth');
Route::post('checktasks', 'TaskController@checkNewTasks')->middleware('auth');
Route::post('endtask', 'TaskController@endTask')->middleware('auth');
Route::post('changetaskpercentage', 'TaskController@changeTaskPercentage')->middleware('auth');
Route::post('canceltask', 'TaskController@cancelTask')->middleware('auth');
Route::post('canceltaskforever', 'TaskController@cancelTaskForEver')->middleware('auth');
Route::post('postponetask', 'TaskController@postponeTask')->middleware('auth');

Route::get('sales', 'SaleController@showSales')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('sale/{id}', 'SaleController@showSale')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/createsale', 'SaleController@create')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/storesale', 'SaleController@store')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::get('/editsale/{id}', 'SaleController@edit')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/updatesale/{id}', 'SaleController@update')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('updatesale');
Route::post('/deletesale', 'SaleController@delete')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/definitivedeletesale', 'SaleController@deleteForEver')->middleware('auth', 'chatdata', 'reminders', 'tasks');


Auth::routes();
Route::get('/home', 'HomeController@homePage')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('home');
Route::get('/panel', 'HomeController@panel')->middleware('auth', 'chatdata', 'reminders', 'tasks');
Route::post('/openfile', 'HomeController@openFile')->middleware('auth');

Route::get('images/{user_id}', 'UserController@avatarImg')->middleware('auth', 'chatdata', 'reminders', 'tasks')->name('avatarImg');
Route::post('chat/showchat', 'MessageController@showChat')->name('showchat')->middleware('auth');
Route::post('chat/checkunreadchat', 'MessageController@checkUnreadChat')->name('checkunread')->middleware('auth');
Route::post('chatsendchat', 'MessageController@sendChat')->name('sendchat')->middleware('auth');

//Route::get('sendmail', 'MailController@sendMail')->middleware('auth', 'chatdata');








/*Route::get('project/{id}', function ($id) { // Busca elemento con el ID en BD
	$users = App\User::all();
	$project = App\Project::find($id); // Toma el valor con ID coincidente de la tabla
	$projectelements = App\Projectelement::where('project_id', '=', $id)->get();
	$elements = App\Element::all();
	//$elementData = ('{id}', 'ProjectelementController@getProjectelements');
	return view('project')->with('project', $project)->with('projectelements', $projectelements)->with('elements', $elements)->with('users', $users);
})->middleware('auth', 'chatdata');*/

//Route::get('/project/{id}', 'ProjectController@getProjectelementsData')->name('project')->middleware('auth', 'chatdata');

/*Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');*/


//REFERENCIAS DE CODIGO

/*Route::get('user/{nombre}/{id}', function ($nombre, $id) {
    return 'user '.$nombre.' ID '.$id;
});*/

/*Route::get('elementos', function () { // Pasa variables a una vista
	$datos = array(
		'project' => 212,
		'subconjunto' => 1,
		'elemento' => 75
	);
	return view('elementos')->with('datos', $datos);
});*/

/*Route::get('project_ID/{Nombre}', function ($nombre) {
	$project = App\project::where('Nombre', '=', $nombre)->first();
	echo $project->ID;
});*/

//Route::get('projects', 'projectController@mostrarprojects');

/*Route::get('project/{id}', function ($id) { // Busca elemento con el ID en BD
	//$project = App\project::first(); // Toma solo el primer valor de la tabla
	$project = App\project::find($id); // Toma el valor con ID coincidente de la tabla
	//echo $project->Nombre;
	return view('project')->with('project', $project);
})->middleware('auth', 'chatdata');*/

/*Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth', 'chatdata');
*/
