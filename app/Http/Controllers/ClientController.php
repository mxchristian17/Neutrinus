<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Client_contact;
use App\Currency;

class ClientController extends Controller
{
  public function showClients($showAll = 0) {
    if(auth()->user()->permissionViewClients->state)
    {
      if($showAll){
        $clients = Client::orderBy('name', 'ASC')->get();
      }else{
        $clients = Client::where('state_id', '=', '1')->orderBy('name', 'ASC')->get();
      }
    }else
    {
      return redirect('projects');
    }

    return view('clients')->with('clients', $clients)->with('showAll', $showAll);
  }

  public function showClient($id) {
    $client = Client::findOrFail($id);
    if(!auth()->user()->permissionViewClients->state OR ($client->state_id == 2 AND !auth()->user()->permissionViewDisabledClients->state) OR ($client->state_id == 3 AND !auth()->user()->permissionViewHiddenClients->state) OR ($client->state_id == 4 AND !auth()->user()->permissionViewDeletedClients->state))
    {
      return redirect('projects');
    }

    return view('client')->with('client', $client);
  }

  public function create()
  {
    if(auth()->user()->permissionCreateClient->state)
    {
      $states = array(1 => 'Cliente habilitado');
      if(auth()->user()->permissionViewDisabledClients->state){
        $states[2] = 'Cliente deshabilitado';
      }
      if(auth()->user()->permissionViewHiddenClients->state){
        $states[3] = 'Cliente oculto';
      }

      $taxPayer = array(1 => 'Monotributista');
      $taxPayer[2] = 'Responsable inscripto';

      $currencies = Currency::all()->pluck('name', 'id');

      return view('clients.create')->with('states', $states)->with('taxpayer_type_id', $taxPayer)->withCurrencies($currencies);
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function store(Request $request)
  {
    if(auth()->user()->permissionCreateClient->state)
    {
      $request->request->add(['author_id' => auth()->user()->id]);
      $request->request->add(['updater_id' => auth()->user()->id]);
      $validatedData = $request->validate([
        '_token' => 'required',
        'name' => 'required',
        'phone_number' => 'regex:/^([0-9\s\-\+\_\(\)]*)$/|min:10|nullable',
        'email' => 'email:rfc,dns|unique:clients|nullable',
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
        'name.required' => 'Es necesario incluir un nombre para el cliente',
        'state_id.required' => 'El estado del cliente está mal definido',
        'state_id.numeric' => 'El estado del cliente está mal definido',
        'state_id.min' => 'El estado del cliente está fuera del rango aceptable',
        'state_id.max' => 'El estado del cliente está fuera del rango aceptable',
        'contact.1.*.max' => 'El nombre de los contactos del cliente no debe exceder los 100 caracteres',
        'contact.2.*.regex' => 'El teléfono de las personas de contacto debe ser un número correcto',
        'contact.2.*.min' => 'El teléfono de las personas de contacto debe ser un número correcto',
        'contact.3.*.email' => 'El mail de las personas de contacto no es correcto'
      ]);
      $client = Client::create($request->except('_token'));
      foreach($request->contact[1] as $key => $name)
      {
        if(!is_null($name))
        {
          $phone_number = $request->contact[2][$key];
          $email = $request->contact[3][$key];
          $contact = new Client_contact;
          $contact->client_id = $client->id;
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
      return redirect("/client/$client->id");
    }else{
      return redirect('neutrinus/error/405');
    }
  }

  public function edit($id)
  {
    $client = Client::findOrFail($id);//where('state_id', '!=', '4')->findOrFail($id);
    if(!auth()->user()->permissionViewClients->state OR !auth()->user()->permissionCreateClient->state OR ($client->state_id == 2 AND !auth()->user()->permissionViewDisabledClients->state) OR ($client->state_id == 3 AND !auth()->user()->permissionViewHiddenClients->state) OR ($client->state_id == 4 AND !auth()->user()->permissionViewDeletedClients->state))
    {
      return redirect('neutrinus/error/405');
    }

    $taxPayer = array(1 => 'Monotributista');
    $taxPayer[2] = 'Responsable inscripto';

    $currencies = Currency::all()->pluck('name', 'id');

    $states = array(1 => 'Cliente habilitado');
    if(auth()->user()->permissionViewDisabledClients->state){ $states[2] = 'Cliente deshabilitado';}
    if(auth()->user()->permissionViewHiddenClients->state){ $states[3] = 'Cliente oculto';}
    if(auth()->user()->permissionViewDeletedClients->state AND auth()->user()->permissionDeleteClient->state){ $states[4] = 'Cliente eliminado';}
    return view('clients.edit')->withClient($client)->with('states', $states)->with('taxpayer_type_id', $taxPayer)->withCurrencies($currencies);
  }

  public function update($id, Request $request)
  {
    $client = Client::findOrFail($id);
    if(!auth()->user()->permissionViewClients->state OR !auth()->user()->permissionCreateClient->state OR ($client->state_id == 2 AND !auth()->user()->permissionViewDisabledClients->state) OR ($client->state_id == 3 AND !auth()->user()->permissionViewHiddenClients->state) OR ($client->state_id == 4 AND !auth()->user()->permissionViewDeletedClients->state))
    {
      return redirect('neutrinus/error/405');
    }
    if($request->state_id==4 AND !auth()->user()->permissionDeleteClient->state)
    {
      return redirect('neutrinus/error/405');
    }
    $request->request->add(['author_id' => $client->author_id]);
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
      'email' => 'email:rfc,dns|unique:clients,email,'.$client->id.'|nullable',
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
      'name.required' => 'Es necesario incluir un nombre para el cliente',
      'state_id.required' => 'El estado del cliente está mal definido',
      'state_id.numeric' => 'El estado del cliente está mal definido',
      'state_id.min' => 'El estado del cliente está fuera del rango aceptable',
      'state_id.max' => 'El estado del cliente está fuera del rango aceptable',
      'contact.1.*.max' => 'El nombre de los contactos del cliente no debe exceder los 100 caracteres',
      'contact.2.*.regex' => 'El teléfono de las personas de contacto debe ser un número correcto',
      'contact.2.*.min' => 'El teléfono de las personas de contacto debe ser un número correcto',
      'contact.3.*.email' => 'El mail de las personas de contacto no es correcto'
    ]);

    $input = $request->except('_token');

    $client->fill($input)->save();
    Client_contact::where('client_id', $client->id)->delete();
    foreach($request->contact[1] as $key => $name)
    {
      if(!is_null($name))
      {
        $phone_number = $request->contact[2][$key];
        $email = $request->contact[3][$key];
        $contact = new Client_contact;
        $contact->client_id = $client->id;
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

    return redirect("client/$request->id");
  }

}
