<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App\User;
use App\Permission;
use App\Personnel_in_charge;
use App\Task;
use App\User_config;
use Auth;
use File;
use Response;
use Storage;
use Image;
use Session;
use App\Role;

class UserController extends Controller
{
  public function AuthRouteAPI(Request $request){
    return $request->user();
  }

  public function showUser ($id) { // Busca elemento con el ID en BD
    $user = User::findOrFail($id); // Toma el valor con ID coincidente de la tabla
    if($user->state_id == 4) return redirect('neutrinus/error/400');
    if(auth()->user()->permissionViewUsersBaseInfo->state OR Gate::allows('seeUsersInformation') OR (auth()->user()->id == $user->id))
    {
      if(isUnderCharge(auth()->user(), $user))
      {
        $tasks = Task::where('user_id', $user->id)->where('task_start', '<', Carbon::now())->where('activated', '1')->get();
        $tasks->showAlert = 'false';
        foreach($tasks as $task)
        {
          if($task->showed == false AND ($task->user_id == auth()->user()->id))
          {
            $task->showed = true;
            $task->save();
            $task->new = true;
            $tasks->showAlert = 'true';
          }else{
            $task->new = false;
          }
        }
      }else{
        $tasks = null;
      }
      return view('user')->with('user', $user)->withUserTasks($tasks);
    }else{
      return redirect('neutrinus/error/405');
    }
  }


  public function editUserPreferences() {
    return view('users.preferences')->withUser(auth()->user());
  }

  public function updateUserPreferences(Request $request)
  {
    $validatedData = $request->validate([
      '_token' => 'required',
      'preference' => 'required|numeric',
      'val' => 'required|numeric|',
    ]);
    switch($request->preference)
    {
      case 1: if(User_config::where('user_id', auth()->user()->id)->update(['show_panel_home' => $request->val])) return 'hecho!'; break;
      case 2: if(User_config::where('user_id', auth()->user()->id)->update(['show_element_general_search' => $request->val])) return 'hecho!'; break;
    }
    return;
  }

  public function showUserPermissionManager () { // Busca elemento con el ID en BD
    if(Gate::allows('editPermissions')){
      $users = User::where('state_id', '<>', 4)->get()->sortBy("name");
      return view('userPermissionManager')->with('users', $users);
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function edit($id)
  {
    $user = User::findOrFail($id);
    if($user->state_id == 4) return redirect('neutrinus/error/400');
    if(!(auth()->user()->id == $id OR Gate::allows('editUsers')))
    {
      return redirect('neutrinus/error/405');
    }
    $relationableUsers = User::whereNull('blocked_date')->where('id', '<>', $id)->get();
    foreach($relationableUsers as $userData)
    {
      $userData->atCharge = isUnderCharge($user, $userData);
      $userData->overCharge = isOverCharge($user, $userData);
    }
    return view('users.edit')->withUser($user)->with('users', $relationableUsers);
  }

  public function update($id, Request $request)
  {
    $user = User::findOrFail($id);
    if($user->state_id == 4) return redirect('neutrinus/error/400');
    if(!(auth()->user()->id == $id OR Gate::allows('editUsers')))
    {
      return redirect('neutrinus/error/405');
    }
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,'.$user->id,
      'phone_number' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
      'date_of_birth' => 'date_format:"Y-m-d"|nullable',
      'image' => 'image|max:2048',
    ],
    [
      'name.required' => 'Es necesario incluir un nombre',
      'last_name.required' => 'Es necesario incluir el apellido',
      'email.required' => 'Es necesario incluir una dirección de correo válida',
      'email.email' => 'Es necesario incluir una dirección de correo válida',
      'email.unique' => 'Ya existe un correo igual registrado por otro usuario',
      'date_of_birth.date_format' => 'La fecha de nacimiento está definida de forma incorrecta. Debe ser del formato yyyy-mm-dd'
    ]);

    if ($request->hasFile('image'))
    {
      $image      = $request->file('image');

      $img = Image::make($image->getRealPath()); //Obtengo imagen
      $img->encode('jpg', 100); //Convierto en JPG con calidad 100%
      $width = $img->width(); //Obtengo ancho en px
      $height = $img->height(); //Obtengo alto en px
      $minDimension = min($width, $height); //Obtengo el minimo entre alto y ancho
      $img->crop($minDimension, $minDimension, (($img->width()-$minDimension)/2), (($img->height()-$minDimension)/2)); //recorto para hacer cuadrada con el corte centrado
      $img->resize(600, 600, function ($constraint) { //Cambio tamaño a 600px x 600px
          $constraint->aspectRatio();
      });

      $img->stream(); // <-- Key point

      $fileName   = $id . '.jpg';// . $image->getClientOriginalExtension();
      //dd();
      Storage::disk('local')->put('files/avatars'.'/'.$fileName, $img, 'private');
    }

    $input = $request->except('_token');

    $user->fill($input)->save();

    return redirect("user/$id");
  }

  public function editUserAtCharge (Request $request) { // Busca elemento con el ID en BD
    if(Gate::allows('editPermissions')){
      $request->atChargeid = intval(str_replace('atCharge', '', $request->atChargeid));
      $personnel_in_charge = Personnel_in_charge::where('user_at_charge_id', $request->uid)->where('user_under_charge_id', $request->atChargeid)->first();
      if($request->state == 0)
      {
        if(Personnel_in_charge::where('id', $personnel_in_charge->id)->delete())
        {
          return 'fue eliminado como personal a cargo';
        }

      }else{
        $relation = new Personnel_in_charge;
        $relation->user_at_charge_id = $request->uid;
        $relation->user_under_charge_id = $request->atChargeid;
        $relation->state_id = 1;
        $relation->author_id = auth()->user()->id;
        $relation->updater_id = auth()->user()->id;
        if($relation->save())
        {
          return 'fue agregado como personal a cargo';
        }

      }
    }else{
      return 'No es posible hacer la modificación';
    }
  }

  public function editPermission (Request $request) { // Busca elemento con el ID en BD
    if(Gate::allows('editPermissions')){
      Permission::where([['user_id', $request->uid], ['code_id', $request->cid]])->update(['state' => $request->state]);
      if($request->state){
        return 'concedido';
      }else{
        return 'anulado';
      }
    }else{
      return 'No es posible hacer la modificación';
    }
  }

  public function setPermissionTemplate ($user_id, $template_id) { // Set de permisos de usuario segun template
    if(Gate::allows('editPermissions')){
      User::findOrFail($user_id);
      switch($template_id){

        case 1: /* Administrador general */
        $permissions = array(
           1 => 1,//permissionViewProjects
           2 => 1,//permissionCreateProject
           3 => 1,//permissionDeleteProject
           4 => 1,//permissionCreateElement
           5 => 1,//permissionCreateProjectelement
           6 => 1,//permissionDeleteElement
           7 => 1,//permissionDeleteProjectelement
           8 => 1,//permissionViewElementPrice
           9 => 1,//permissionEditElementPrice
          10 => 1,//permissionViewDisabledProjects
          11 => 1,//permissionViewHiddenProjects
          12 => 1,//permissionViewDisabledElements
          13 => 1,//permissionViewHiddenElements
          14 => 1,//permissionViewDeletedProjects
          15 => 1,//permissionCreateSubset
          16 => 1,//permissionDeleteSubset
          17 => 1,//permissionViewDisabledSubsets
          18 => 1,//permissionViewHiddenSubsets
          19 => 1,//permissionViewDeletedSubsets
          20 => 1,//permissionViewDeletedElements
          21 => 1,//permissionViewElements
          22 => 1,//permissionCreateAppliedElement
          23 => 1,//permissionViewMaterials
          24 => 1,//permissionViewDisabledMaterials
          25 => 1,//permissionViewHiddenMaterials
          26 => 1,//permissionViewDeletedMaterials
          27 => 1,//permissionCreateMaterial
          28 => 1,//permissionDeleteMaterial
          29 => 1,//permissionViewOrder_types
          30 => 1,//permissionViewDisabledOrder_types
          31 => 1,//permissionViewHiddenOrder_types
          32 => 1,//permissionViewDeletedOrder_types
          33 => 1,//permissionCreateOrder_type
          34 => 1,//permissionDeleteOrder_type
          35 => 1,//permissionViewOperations
          36 => 1,//permissionViewDisabledOperations
          37 => 1,//permissionViewHiddenOperations
          38 => 1,//permissionViewDeletedOperations
          39 => 1,//permissionCreateOperation
          40 => 1,//permissionDeleteOperation
          41 => 1,//permissionViewSuppliers
          42 => 1,//permissionViewDisabledSuppliers
          43 => 1,//permissionViewHiddenSuppliers
          44 => 1,//permissionViewDeletedSuppliers
          45 => 1,//permissionCreateSupplier
          46 => 1,//permissionDeleteSupplier
          47 => 1,//permissionViewClients
          48 => 1,//permissionViewDisabledClients
          49 => 1,//permissionViewHiddenClients
          50 => 1,//permissionViewDeletedClients
          51 => 1,//permissionCreateClient
          52 => 1,//permissionDeleteClient
          53 => 1,//permissionViewElementsExt_f_1
          54 => 1,//permissionViewSubsetsExt_f_1
          55 => 1,//permissionViewProjectsExt_f_1
          56 => 1,//permissionViewElementsExt_f_2
          57 => 1,//permissionViewSubsetsExt_f_2
          58 => 1,//permissionViewProjectsExt_f_2
          59 => 1,//permissionViewElementsExt_f_3
          60 => 1,//permissionViewSubsetsExt_f_3
          61 => 1,//permissionViewProjectsExt_f_3
          62 => 1,//permissionViewOperationPrice
          63 => 1,//permissionViewProjectStats
          64 => 1,//permissionCreateMaterialPrice
          65 => 1,//permissionViewMaterialPrices
          66 => 1,//permissionUseChat
          67 => 1,//permissionViewPurchase_Orders
          68 => 1,//permissionCreatePurchase_Order
          69 => 1,//permissionDeletePurchase_Order
          70 => 1,//permissionAwardPurchase_Order
          71 => 1,//permissionReceivePurchase_Order
          72 => 1,//permissionViewPurchase_OrderPrices
          73 => 1,//permissionViewUsersBaseInfo
          74 => 1,//permissionUseReminders
          75 => 1,//permissionUseTasks
          76 => 1,//permissionAssignTasks
          77 => 1,//permissionViewSales
          78 => 1,//permissionViewDisabledSales
          79 => 1,//permissionViewHiddenSales
          80 => 1,//permissionViewDeletedSales
          81 => 1,//permissionCreateSale
          82 => 1,//permissionDeleteSale
          83 => 1,//permissionViewOwedItems
          84 => 1,//permissionViewProjectFolder
          85 => 1,//permissionViewElementFolder
          86 => 1,//permissionViewOperationFolder
          87 => 1,//permissionViewCash_Flow
          88 => 1,//permissionEditSupplier_code
        );
        break;

        case 2: /* Ingeniería */
        $permissions = array(
           1 => 1,//permissionViewProjects
           2 => 1,//permissionCreateProject
           3 => 0,//permissionDeleteProject
           4 => 1,//permissionCreateElement
           5 => 1,//permissionCreateProjectelement
           6 => 1,//permissionDeleteElement
           7 => 1,//permissionDeleteProjectelement
           8 => 1,//permissionViewElementPrice
           9 => 1,//permissionEditElementPrice
          10 => 1,//permissionViewDisabledProjects
          11 => 0,//permissionViewHiddenProjects
          12 => 1,//permissionViewDisabledElements
          13 => 0,//permissionViewHiddenElements
          14 => 1,//permissionViewDeletedProjects
          15 => 1,//permissionCreateSubset
          16 => 1,//permissionDeleteSubset
          17 => 1,//permissionViewDisabledSubsets
          18 => 0,//permissionViewHiddenSubsets
          19 => 1,//permissionViewDeletedSubsets
          20 => 1,//permissionViewDeletedElements
          21 => 1,//permissionViewElements
          22 => 1,//permissionCreateAppliedElement
          23 => 1,//permissionViewMaterials
          24 => 1,//permissionViewDisabledMaterials
          25 => 0,//permissionViewHiddenMaterials
          26 => 1,//permissionViewDeletedMaterials
          27 => 1,//permissionCreateMaterial
          28 => 1,//permissionDeleteMaterial
          29 => 1,//permissionViewOrder_types
          30 => 1,//permissionViewDisabledOrder_types
          31 => 0,//permissionViewHiddenOrder_types
          32 => 1,//permissionViewDeletedOrder_types
          33 => 1,//permissionCreateOrder_type
          34 => 1,//permissionDeleteOrder_type
          35 => 1,//permissionViewOperations
          36 => 1,//permissionViewDisabledOperations
          37 => 0,//permissionViewHiddenOperations
          38 => 1,//permissionViewDeletedOperations
          39 => 1,//permissionCreateOperation
          40 => 1,//permissionDeleteOperation
          41 => 1,//permissionViewSuppliers
          42 => 0,//permissionViewDisabledSuppliers
          43 => 0,//permissionViewHiddenSuppliers
          44 => 0,//permissionViewDeletedSuppliers
          45 => 0,//permissionCreateSupplier
          46 => 0,//permissionDeleteSupplier
          47 => 1,//permissionViewClients
          48 => 0,//permissionViewDisabledClients
          49 => 0,//permissionViewHiddenClients
          50 => 0,//permissionViewDeletedClients
          51 => 0,//permissionCreateClient
          52 => 0,//permissionDeleteClient
          53 => 1,//permissionViewElementsExt_f_1
          54 => 1,//permissionViewSubsetsExt_f_1
          55 => 1,//permissionViewProjectsExt_f_1
          56 => 1,//permissionViewElementsExt_f_2
          57 => 1,//permissionViewSubsetsExt_f_2
          58 => 1,//permissionViewProjectsExt_f_2
          59 => 1,//permissionViewElementsExt_f_3
          60 => 1,//permissionViewSubsetsExt_f_3
          61 => 1,//permissionViewProjectsExt_f_3
          62 => 1,//permissionViewOperationPrice
          63 => 1,//permissionViewProjectStats
          64 => 1,//permissionCreateMaterialPrice
          65 => 1,//permissionViewMaterialPrices
          66 => 1,//permissionUseChat
          67 => 1,//permissionViewPurchase_Orders
          68 => 1,//permissionCreatePurchase_Order
          69 => 1,//permissionDeletePurchase_Order
          70 => 0,//permissionAwardPurchase_Order
          71 => 1,//permissionReceivePurchase_Order
          72 => 1,//permissionViewPurchase_OrderPrices
          73 => 1,//permissionViewUsersBaseInfo
          74 => 1,//permissionUseReminders
          75 => 1,//permissionUseTasks
          76 => 1,//permissionAssignTasks
          77 => 0,//permissionViewSales
          78 => 0,//permissionViewDisabledSales
          79 => 0,//permissionViewHiddenSales
          80 => 0,//permissionViewDeletedSales
          81 => 0,//permissionCreateSale
          82 => 0,//permissionDeleteSale
          83 => 1,//permissionViewOwedItems
          84 => 1,//permissionViewProjectFolder
          85 => 1,//permissionViewElementFolder
          86 => 1,//permissionViewOperationFolder
          87 => 0,//permissionViewCash_Flow
          88 => 1,//permissionEditSupplier_code
        );
        break;

        case 3: /* Técnico de oficina técnica */
        $permissions = array(
           1 => 1,//permissionViewProjects
           2 => 1,//permissionCreateProject
           3 => 0,//permissionDeleteProject
           4 => 1,//permissionCreateElement
           5 => 1,//permissionCreateProjectelement
           6 => 1,//permissionDeleteElement
           7 => 1,//permissionDeleteProjectelement
           8 => 0,//permissionViewElementPrice
           9 => 0,//permissionEditElementPrice
          10 => 1,//permissionViewDisabledProjects
          11 => 0,//permissionViewHiddenProjects
          12 => 1,//permissionViewDisabledElements
          13 => 0,//permissionViewHiddenElements
          14 => 0,//permissionViewDeletedProjects
          15 => 1,//permissionCreateSubset
          16 => 1,//permissionDeleteSubset
          17 => 1,//permissionViewDisabledSubsets
          18 => 0,//permissionViewHiddenSubsets
          19 => 0,//permissionViewDeletedSubsets
          20 => 0,//permissionViewDeletedElements
          21 => 1,//permissionViewElements
          22 => 1,//permissionCreateAppliedElement
          23 => 1,//permissionViewMaterials
          24 => 1,//permissionViewDisabledMaterials
          25 => 0,//permissionViewHiddenMaterials
          26 => 0,//permissionViewDeletedMaterials
          27 => 1,//permissionCreateMaterial
          28 => 1,//permissionDeleteMaterial
          29 => 1,//permissionViewOrder_types
          30 => 1,//permissionViewDisabledOrder_types
          31 => 0,//permissionViewHiddenOrder_types
          32 => 0,//permissionViewDeletedOrder_types
          33 => 1,//permissionCreateOrder_type
          34 => 1,//permissionDeleteOrder_type
          35 => 1,//permissionViewOperations
          36 => 1,//permissionViewDisabledOperations
          37 => 0,//permissionViewHiddenOperations
          38 => 0,//permissionViewDeletedOperations
          39 => 1,//permissionCreateOperation
          40 => 1,//permissionDeleteOperation
          41 => 1,//permissionViewSuppliers
          42 => 0,//permissionViewDisabledSuppliers
          43 => 0,//permissionViewHiddenSuppliers
          44 => 0,//permissionViewDeletedSuppliers
          45 => 0,//permissionCreateSupplier
          46 => 0,//permissionDeleteSupplier
          47 => 0,//permissionViewClients
          48 => 0,//permissionViewDisabledClients
          49 => 0,//permissionViewHiddenClients
          50 => 0,//permissionViewDeletedClients
          51 => 0,//permissionCreateClient
          52 => 0,//permissionDeleteClient
          53 => 1,//permissionViewElementsExt_f_1
          54 => 1,//permissionViewSubsetsExt_f_1
          55 => 1,//permissionViewProjectsExt_f_1
          56 => 1,//permissionViewElementsExt_f_2
          57 => 1,//permissionViewSubsetsExt_f_2
          58 => 1,//permissionViewProjectsExt_f_2
          59 => 1,//permissionViewElementsExt_f_3
          60 => 1,//permissionViewSubsetsExt_f_3
          61 => 1,//permissionViewProjectsExt_f_3
          62 => 0,//permissionViewOperationPrice
          63 => 0,//permissionViewProjectStats
          64 => 0,//permissionCreateMaterialPrice
          65 => 0,//permissionViewMaterialPrices
          66 => 1,//permissionUseChat
          67 => 1,//permissionViewPurchase_Orders
          68 => 1,//permissionCreatePurchase_Order
          69 => 0,//permissionDeletePurchase_Order
          70 => 0,//permissionAwardPurchase_Order
          71 => 1,//permissionReceivePurchase_Order
          72 => 0,//permissionViewPurchase_OrderPrices
          73 => 1,//permissionViewUsersBaseInfo
          74 => 1,//permissionUseReminders
          75 => 1,//permissionUseTasks
          76 => 0,//permissionAssignTasks
          77 => 0,//permissionViewSales
          78 => 0,//permissionViewDisabledSales
          79 => 0,//permissionViewHiddenSales
          80 => 0,//permissionViewDeletedSales
          81 => 0,//permissionCreateSale
          82 => 0,//permissionDeleteSale
          83 => 1,//permissionViewOwedItems
          84 => 1,//permissionViewProjectFolder
          85 => 1,//permissionViewElementFolder
          86 => 1,//permissionViewOperationFolder
          87 => 0,//permissionViewCash_Flow
          88 => 0,//permissionEditSupplier_code
        );
        break;

        case 4: /* Ejecutivo de compras */
        $permissions = array(
           1 => 1,//permissionViewProjects
           2 => 0,//permissionCreateProject
           3 => 0,//permissionDeleteProject
           4 => 0,//permissionCreateElement
           5 => 0,//permissionCreateProjectelement
           6 => 0,//permissionDeleteElement
           7 => 0,//permissionDeleteProjectelement
           8 => 1,//permissionViewElementPrice
           9 => 1,//permissionEditElementPrice
          10 => 0,//permissionViewDisabledProjects
          11 => 0,//permissionViewHiddenProjects
          12 => 0,//permissionViewDisabledElements
          13 => 0,//permissionViewHiddenElements
          14 => 0,//permissionViewDeletedProjects
          15 => 0,//permissionCreateSubset
          16 => 0,//permissionDeleteSubset
          17 => 0,//permissionViewDisabledSubsets
          18 => 0,//permissionViewHiddenSubsets
          19 => 0,//permissionViewDeletedSubsets
          20 => 0,//permissionViewDeletedElements
          21 => 1,//permissionViewElements
          22 => 0,//permissionCreateAppliedElement
          23 => 1,//permissionViewMaterials
          24 => 0,//permissionViewDisabledMaterials
          25 => 0,//permissionViewHiddenMaterials
          26 => 0,//permissionViewDeletedMaterials
          27 => 0,//permissionCreateMaterial
          28 => 0,//permissionDeleteMaterial
          29 => 1,//permissionViewOrder_types
          30 => 0,//permissionViewDisabledOrder_types
          31 => 0,//permissionViewHiddenOrder_types
          32 => 0,//permissionViewDeletedOrder_types
          33 => 0,//permissionCreateOrder_type
          34 => 0,//permissionDeleteOrder_type
          35 => 0,//permissionViewOperations
          36 => 0,//permissionViewDisabledOperations
          37 => 0,//permissionViewHiddenOperations
          38 => 0,//permissionViewDeletedOperations
          39 => 0,//permissionCreateOperation
          40 => 0,//permissionDeleteOperation
          41 => 1,//permissionViewSuppliers
          42 => 1,//permissionViewDisabledSuppliers
          43 => 0,//permissionViewHiddenSuppliers
          44 => 1,//permissionViewDeletedSuppliers
          45 => 1,//permissionCreateSupplier
          46 => 1,//permissionDeleteSupplier
          47 => 0,//permissionViewClients
          48 => 0,//permissionViewDisabledClients
          49 => 0,//permissionViewHiddenClients
          50 => 0,//permissionViewDeletedClients
          51 => 0,//permissionCreateClient
          52 => 0,//permissionDeleteClient
          53 => 1,//permissionViewElementsExt_f_1
          54 => 1,//permissionViewSubsetsExt_f_1
          55 => 1,//permissionViewProjectsExt_f_1
          56 => 1,//permissionViewElementsExt_f_2
          57 => 1,//permissionViewSubsetsExt_f_2
          58 => 1,//permissionViewProjectsExt_f_2
          59 => 1,//permissionViewElementsExt_f_3
          60 => 1,//permissionViewSubsetsExt_f_3
          61 => 1,//permissionViewProjectsExt_f_3
          62 => 0,//permissionViewOperationPrice
          63 => 1,//permissionViewProjectStats
          64 => 1,//permissionCreateMaterialPrice
          65 => 1,//permissionViewMaterialPrices
          66 => 1,//permissionUseChat
          67 => 1,//permissionViewPurchase_Orders
          68 => 1,//permissionCreatePurchase_Order
          69 => 1,//permissionDeletePurchase_Order
          70 => 1,//permissionAwardPurchase_Order
          71 => 1,//permissionReceivePurchase_Order
          72 => 1,//permissionViewPurchase_OrderPrices
          73 => 1,//permissionViewUsersBaseInfo
          74 => 1,//permissionUseReminders
          75 => 1,//permissionUseTasks
          76 => 0,//permissionAssignTasks
          77 => 0,//permissionViewSales
          78 => 0,//permissionViewDisabledSales
          79 => 0,//permissionViewHiddenSales
          80 => 0,//permissionViewDeletedSales
          81 => 0,//permissionCreateSale
          82 => 0,//permissionDeleteSale
          83 => 1,//permissionViewOwedItems
          84 => 1,//permissionViewProjectFolder
          85 => 1,//permissionViewElementFolder
          86 => 1,//permissionViewOperationFolder
          87 => 1,//permissionViewCash_Flow
          88 => 1,//permissionEditSupplier_code
        );
        break;

        default:
        $permissions = array();
        break;
      }
      foreach($permissions as $key => $permission)
      {
        Permission::where([['user_id', $user_id], ['code_id', $key]])->update(['state' => $permission]);
      }
    }else{
      return redirect('neutrinus/error/405');
    }

    return redirect(url()->previous());
  }

  public function editUserAuthLevel (Request $request) { // Busca elemento con el ID en BD
    if((Gate::allows('editPermissions')) AND ($request->rid == 1 OR $request->rid == 2))
    {
      $user = User::findOrFail($request->uid);
      if($user->state_id == 4) return redirect('neutrinus/error/400');
      foreach ($user->roles as $role)
      {
          $role->pivot->role_id = $request->rid;
          $saved = $role->pivot->save();
      }
      if($saved){
        return ' ahora es ';
      }else{
        return ' no se ha podido cambiar a ';
      }
    }else{
      return 'No es posible hacer la modificación';
    }
  }

  public function editUserStatus (Request $request) { // Busca elemento con el ID en BD
    if((Gate::allows('editUserStatus')) AND ($request->rid == 0 OR $request->rid == 1) AND (Gate::allows('deleteUsers')))
    {
      $user = User::findOrFail($request->uid);
      if($user->state_id == 4) return redirect('neutrinus/error/400');
      if($request->rid == 0)
      {
        $user->blocked_date = Carbon::now()->add(1000000, 'day');
      }else{
        $user->blocked_date = null;
      }
      $saved = $user->save();
      if($saved){
        return ' ahora está ';
      }else{
        return ' no se ha podido cambiar a ';
      }
    }else{
      return 'No es posible hacer la modificación';
    }
  }

  public function avatarImg($user_id) {
    $path = storage_path('app\files\avatars') . '/' . $user_id;
    if (!file_exists($path)) {
        $user = User::findOrFail($user_id);
        if($user->state_id == 4) return redirect('neutrinus/error/400');
        switch($user->gender)
        {
          case 'M': $path = storage_path('app\files\avatars') . '/standardMale.png'; break;
          case 'F': $path = storage_path('app\files\avatars') . '/standardFemale.png'; break;
          case 'O': $path = storage_path('app\files\avatars') . '/standardOther.png'; break;
          default: $path = storage_path('app\files\avatars') . '/standardOther.png'; break;
        }
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

     return $response;
  }

  public function deleteUser($id)
  {
    if(!(Gate::allows('deleteUsers'))) return redirect('neutrinus/error/405');
    $user = User::findOrFail($id);
    $user->name = 'Usuario eliminado';
    $user->last_name = '';
    $user->email = $user->id;
    $user->branch_office = '';
    $user->address = '';
    $user->city = '';
    $user->country = '';
    $user->phone_number = '';
    $user->date_of_birth = null;
    $user->state_id = 4;
    $user->blocked_date = Carbon::now();
    if($user->save())
    {
      Permission::where('user_id', $user->id)->update(['state' => 0]);
      Storage::delete(['files/avatars/' . $user->id . '.jpg']);
      Personnel_in_charge::where('user_at_charge_id', $user->id)->orWhere('user_under_charge_id', $user->id)->delete();
      $user->roles()->detach(Role::all());
    }
    Session::flash('message.level', 'success');
    Session::flash('status', 'El usuario ha sido eliminado de Neutrinus con exito.');
    return redirect('home');
  }

}
