<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function badRequest()
    {
      return view('errors.badrequest');
    }

    public function notAllowed()
    {
    	return view('errors.notallowed');
    }
}
