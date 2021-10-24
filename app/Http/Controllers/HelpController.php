<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function elements()
    {
      return view('help.elements');
    }

    public function order_types()
    {
      return view('help.order_types');
    }

    public function operations()
    {
      return view('help.operations');
    }
}
