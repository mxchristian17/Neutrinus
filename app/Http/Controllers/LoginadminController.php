<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LoginadminController extends Controller
{

  public function startserver()
  {
  	return view('loginAdmin');
  }

  public function login (Request $pass) {
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    $pass = md5($pass->input('password'));
    $servername = env('DB_HOST');
    $username = env('DB_USERNAME');

    // Create connection
    $conn = new mysqli($servername, $username, $pass);
    // Check connection
    if ($conn->connect_error) {
      return redirect('/startserver');
    }
    $pd = 'S'.md5(date('Y-m-d'));
    //$pass = $pass.$pd;
    $myfile = fopen("../.keyFile", "w");
  	fwrite($myfile,$pass.$pd);
  	fclose($myfile);
    return $this->showStartPage();
  }

  public function showStartPage() {

    return redirect()->action('ProjectController@showProjects');
    exit;
  }

}
