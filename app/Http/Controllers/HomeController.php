<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\User;
use App\Recent_project;
use App\Purchase;
use App\Sale;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function homePage()
    {
      if(auth()->user()->config->show_panel_home)
      {
        return redirect('projects');
      }else{
        return $this->panel();
      }
    }
    public function index()
    {
      if(auth()->user()->config->show_panel_home)
      {
        return redirect('projects');
      }else{
        return $this->panel();
      }
    }
    public function panel()
    {
      $recentProjects = Recent_project::select('project_id')->distinct()->where('user_id', auth()->user()->id)->orderBy('visited', 'DESC')->limit('15')->get();
      $usersUnderCharge = auth()->user()->under_charge;
      $purchase_orders = Purchase::where('status', '<', 5)->orderBy('status', 'ASC')->orderByDesc('order_number')->get();
      if(auth()->user()->permissionViewSales->state AND auth()->user()->permissionViewClients->state) {
        $sales = Sale::where('status', '<', 6)->orderBy('created_at', 'DESC')->orderBy('status', 'ASC')->get();
      }else{
        $sales = null;
      }
      return view('home')->withProjects($recentProjects)->withUnderChargeUsers($usersUnderCharge)->withPurchases($purchase_orders)->withSales($sales);
    }

    public function openFile(Request $request)
    {
      $validatedData = $request->validate([
        '_token' => 'required',
        'type' => 'required|numeric|min:0',
        'id' => 'required|numeric|min:0'
      ]);

      switch($request->type)
      {
        case 1: //Project folders
          if(!auth()->user()->permissionViewProjectFolder->state)
          {
            return ('../neutrinus/error/405');
          }
          $address = config('constants.serverFilesAddress').'Proyectos\\'.$request->id;
          if(!is_dir($address)){
              mkdir($address, 0755, true);
              mkdir($address.'\\0-Diseño', 0755);
              mkdir($address.'\\1-Calculos', 0755);
              mkdir($address.'\\2-Fotos', 0755);
              mkdir($address.'\\3-Videos', 0755);
              mkdir($address.'\\4-Manuales', 0755);
              mkdir($address.'\\5-Registro-de-fallas', 0755);
              mkdir($address.'\\6-Control-de-calidad', 0755);
              mkdir($address.'\\7-Otros', 0755);
              mkdir($address.'\\8-Backup', 0755);
          }
        break;
        case 2: //Element folders
          $element = elementIsVisible($request->id);
          if(!auth()->user()->permissionViewElementFolder->state or !$element)
          {
            return ('../neutrinus/error/405');
          }
          $address = config('constants.serverFilesAddress').'Elementos\\'.$request->id;
          if(!is_dir($address)){
              mkdir($address, 0755, true);
              mkdir($address.'\\0-Diseño', 0755);
              mkdir($address.'\\1-Calculos', 0755);
              mkdir($address.'\\2-Fotos', 0755);
              mkdir($address.'\\3-Videos', 0755);
              mkdir($address.'\\4-Manuales', 0755);
              mkdir($address.'\\5-Registro-de-fallas', 0755);
              mkdir($address.'\\6-Control-de-calidad', 0755);
              mkdir($address.'\\7-Otros', 0755);
              mkdir($address.'\\8-Backup', 0755);
          }
        break;
        case 3:
          $operation = operationIsVisible($request->id);
          if(!auth()->user()->permissionViewOperationFolder->state or !$operation)
          {
            return ('../neutrinus/error/405');
          }
          $address = config('constants.serverFilesAddress').'Operaciones/'.$request->id;
          if(!is_dir($address)){
              mkdir($address, 0755, true);
              }
        break;
        default: return; break;
      }

      $file = "neuappsfile.neu";
      $txt = fopen($file, "w") or die("Unable to open file!");
      fwrite($txt, xor_encrypt('start /MAX "" "'.$address.'"'));
      fclose($txt);
      echo $file;
      return;
    }
}
