<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use App\Supplier_contact;
use App\Currency;

class SupplierController extends Controller
{

  public function showSuppliers($showAll = 0) {
    if(auth()->user()->permissionViewSuppliers->state)
    {
      if($showAll){
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
      }else{
        $suppliers = Supplier::where('state_id', '=', '1')->orderBy('name', 'ASC')->get();
      }
    }else
    {
      return redirect('neutrinus/error/405');
    }

    return view('suppliers')->with('suppliers', $suppliers)->with('showAll', $showAll);
  }

  public function showSupplier($id) {
    $supplier = Supplier::findOrFail($id);
    if(!auth()->user()->permissionViewSuppliers->state OR ($supplier->state_id == 2 AND !auth()->user()->permissionViewDisabledSuppliers->state) OR ($supplier->state_id == 3 AND !auth()->user()->permissionViewHiddenSuppliers->state) OR ($supplier->state_id == 4 AND !auth()->user()->permissionViewDeletedSuppliers->state))
    {
      return redirect('neutrinus/error/405');
    }

    return view('supplier')->with('supplier', $supplier);

  }

  public function create()
  {
    if(auth()->user()->permissionCreateSupplier->state)
    {
      $states = array(1 => 'Proveedor habilitado');
      if(auth()->user()->permissionViewDisabledSuppliers->state){
        $states[2] = 'Proveedor deshabilitado';
      }
      if(auth()->user()->permissionViewHiddenSuppliers->state){
        $states[3] = 'Proveedor oculto';
      }

      $taxPayer = array(1 => 'Monotributista');
      $taxPayer[2] = 'Responsable inscripto';

      $currencies = Currency::all()->pluck('name', 'id');

      return view('suppliers.create')->with('states', $states)->with('taxpayer_type_id', $taxPayer)->withCurrencies($currencies);
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function store(Request $request)
  {
    if(auth()->user()->permissionCreateSupplier->state)
    {
      $request->request->add(['author_id' => auth()->user()->id]);
      $request->request->add(['updater_id' => auth()->user()->id]);
      $validatedData = $request->validate([
        '_token' => 'required',
        'name' => 'required',
        'phone_number' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
        'email' => 'email:rfc,dns|unique:suppliers|nullable',
        'cuit' => 'numeric|nullable',
        'taxpayer_type_id' => 'required|numeric|min:1|max:2',
        'address' => 'string|nullable',
        'city' => 'string|nullable',
        'province' => 'string|nullable',
        'country' => 'string|nullable',
        'currency_id' => 'required|numeric|min:1|max:5',
        'description' => 'string|nullable',
        'state_id' => 'required|numeric|min:1|max:3',
        'author_id' => 'required|numeric',
        'updater_id' => 'required|numeric',

        'contact.1.*' => 'string|max:100|nullable',
        'contact.2.*' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
        'contact.3.*' => 'email:rfc,dns|nullable',
      ],
      [
        'name.required' => 'Es necesario incluir un nombre para el proveedor',
        'state_id.required' => 'El estado del proveedor está mal definido',
        'state_id.numeric' => 'El estado del proveedor está mal definido',
        'state_id.min' => 'El estado del proveedor está fuera del rango aceptable',
        'state_id.max' => 'El estado del proveedor está fuera del rango aceptable',
        'contact.1.*.max' => 'El nombre de los contactos del proveedor no debe exceder los 100 caracteres',
        'contact.2.*.regex' => 'El teléfono de las personas de contacto debe ser un número correcto',
        'contact.2.*.min' => 'El teléfono de las personas de contacto debe ser un número correcto',
        'contact.3.*.email' => 'El mail de las personas de contacto no es correcto'
      ]);
      $supplier = Supplier::create($request->except('_token'));
      foreach($request->contact[1] as $key => $name)
      {
        if(!is_null($name))
        {
          $phone_number = $request->contact[2][$key];
          $email = $request->contact[3][$key];
          $contact = new Supplier_contact;
          $contact->supplier_id = $supplier->id;
          $contact->name = $name;
          $contact->phone_number = $phone_number;
          $contact->email = $email;
          $contact->state_id = 1;
          $contact->description = '';
          $contact->author_id = auth()->user()->id;
          $contact->updater_id = auth()->user()->id;
          $contact->save();
        }
      }
      return redirect("/supplier/$supplier->id");
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function edit($id)
  {
    $supplier = Supplier::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewSuppliers->state OR !auth()->user()->permissionCreateSupplier->state OR ($supplier->state_id == 2 AND !auth()->user()->permissionViewDisabledSuppliers->state) OR ($supplier->state_id == 3 AND !auth()->user()->permissionViewHiddenSuppliers->state) OR ($supplier->state_id == 4 AND !auth()->user()->permissionViewDeletedSuppliers->state))
    {
      return redirect('neutrinus/error/405');
    }

    $taxPayer = array(1 => 'Monotributista');
    $taxPayer[2] = 'Responsable inscripto';

    $currencies = Currency::all()->pluck('name', 'id');

    $states = array(1 => 'Proveedor habilitado');
    if(auth()->user()->permissionViewDisabledSuppliers->state){ $states[2] = 'Proveedor deshabilitado';}
    if(auth()->user()->permissionViewHiddenSuppliers->state){ $states[3] = 'Proveedor oculto';}
    if(auth()->user()->permissionViewDeletedSuppliers->state AND auth()->user()->permissionDeleteSupplier->state){ $states[4] = 'Proveedor eliminado';}
    return view('suppliers.edit')->withSupplier($supplier)->with('states', $states)->with('taxpayer_type_id', $taxPayer)->withCurrencies($currencies);
  }

  public function update($id, Request $request)
  {
    $supplier = Supplier::findOrFail($id);
    if(!auth()->user()->permissionViewSuppliers->state OR !auth()->user()->permissionCreateSupplier->state OR ($supplier->state_id == 2 AND !auth()->user()->permissionViewDisabledSuppliers->state) OR ($supplier->state_id == 3 AND !auth()->user()->permissionViewHiddenSuppliers->state) OR ($supplier->state_id == 4 AND !auth()->user()->permissionViewDeletedSuppliers->state))
    {
      return redirect('neutrinus/error/405');
    }
    if($request->state_id==4 AND !auth()->user()->permissionDeleteSupplier->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['author_id' => $supplier->author_id]);
    $request->request->add(['updater_id' => auth()->user()->id]);
    if(intval(str_replace(['+', '(', ')', '_', '-', ' '], '', $request->phone_number)) == 549)
    {
      $request->merge([
          'phone_number' => null,
      ]);
    }
    $validatedData = $request->validate([
      '_token' => 'required',
      'name' => 'required',
      'phone_number' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
      'email' => 'email:rfc,dns|unique:suppliers,email,'.$supplier->id.'|nullable',
      'cuit' => 'numeric|nullable',
      'taxpayer_type_id' => 'required|numeric|min:1|max:2',
      'address' => 'string|nullable',
      'city' => 'string|nullable',
      'province' => 'string|nullable',
      'country' => 'string|nullable',
      'currency_id' => 'required|numeric|min:1|max:5',
      'description' => 'string|nullable',
      'state_id' => 'required|numeric|min:1|max:3',
      'author_id' => 'required|numeric',
      'updater_id' => 'required|numeric',

      'contact.1.*' => 'string|max:100|nullable',
      'contact.2.*' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
      'contact.3.*' => 'email:rfc,dns|nullable',
    ],
    [
      'name.required' => 'Es necesario incluir un nombre para el proveedor',
      'state_id.required' => 'El estado del proveedor está mal definido',
      'state_id.numeric' => 'El estado del proveedor está mal definido',
      'state_id.min' => 'El estado del proveedor está fuera del rango aceptable',
      'state_id.max' => 'El estado del proveedor está fuera del rango aceptable',
      'contact.1.*.max' => 'El nombre de los contactos del proveedor no debe exceder los 100 caracteres',
      'contact.2.*.regex' => 'El teléfono de las personas de contacto debe ser un número correcto',
      'contact.2.*.min' => 'El teléfono de las personas de contacto debe ser un número correcto',
      'contact.3.*.email' => 'El mail de las personas de contacto no es correcto'
    ]);

    $input = $request->except('_token');

    $supplier->fill($input)->save();
    Supplier_contact::where('supplier_id', $supplier->id)->delete();
    foreach($request->contact[1] as $key => $name)
    {
      if(!is_null($name))
      {
        $phone_number = $request->contact[2][$key];
        $email = $request->contact[3][$key];
        $contact = new Supplier_contact;
        $contact->supplier_id = $supplier->id;
        $contact->name = $name;
        $contact->phone_number = $phone_number;
        $contact->email = $email;
        $contact->state_id = 1;
        $contact->description = '';
        $contact->author_id = auth()->user()->id;
        $contact->updater_id = auth()->user()->id;
        $contact->save();
      }
    }

    return redirect("supplier/$request->id");
  }

  public function fetch(Request $request)
  {
   if($request->get('query'))
   {
    $query = $request->get('query');
    if(($query!='') AND (auth()->user()->permissionViewSuppliers->state)){
      $data = Supplier::where('name', 'LIKE', "%{$query}%")->get();
      $output = '<ul id="supplierSelector" class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $key => $row)
      {
        $showSupplier = false;
        switch($row->state_id){
          case 1:
          $showSupplier = true;
          break;
          case 3:
            if(auth()->user()->permissionViewHiddenSuppliers->state){
              $showSupplier = true;
            }
          break;
        }

        if($showSupplier){
          $output .= '
          <li class="selectable" id="su_'.$row->id.'" val="'.$row->id.'">'.$row->name.'</li>
          ';
        }else{
          unset($data[$key]);
        }
      }
      $output .= '</ul>';
      if(count($data)==0)
      {
        $output = '';
      }
    }else{
      $output = '';
    }
    echo $output;
   }
  }

}
