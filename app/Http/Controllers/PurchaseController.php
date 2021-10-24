<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\Purchase_element;
use Cart;
use App\Element;
use App\Supplier;
use Session;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class PurchaseController extends Controller
{
    public function showPurchaseOrders ($supplier = 0)
    {
      if(auth()->user()->permissionViewPurchase_Orders->state)
      {
        if($supplier){
          $purchase_orders = Purchase::where('supplier_id', '=', $supplier)->orderBy('status', 'ASC')->orderByDesc('order_number')->paginate(100);
          $supplierData = Supplier::findOrFail($supplier);
          return view('purchase_orders')->with('purchase_orders', $purchase_orders)->withSupplier($supplierData);
        }else{
          $purchase_orders = Purchase::orderBy('status', 'ASC')->orderByDesc('order_number')->paginate(100);
          return view('purchase_orders')->with('purchase_orders', $purchase_orders)->withSupplier(false);
        }
      }else{
        return redirect('neutrinus/error/405');
      }
    }

    public function showPurchaseOrder ($id)
    {
      if(auth()->user()->permissionViewPurchase_Orders->state)
      {
        $purchase_order = Purchase::findOrFail($id);
      }else{
        return redirect('neutrinus/error/405');
      }
      return view('purchase_order')->with('purchase_order', $purchase_order);
    }

    public function storePurchaseOrder ($status = 0)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return redirect('neutrinus/error/405');
      if(!auth()->user()->permissionAwardPurchase_Order->state AND $status != 0) return redirect('neutrinus/error/405');
      $suppliers = session()->get('cartSuppliers');
      $n=0;
      if(is_array($suppliers))
      {
        foreach($suppliers as $key => $supplier){$n++;}
        if($n > 1 AND $status !=0)
        {
          Session::flash('message.level', 'danger');
          Session::flash('status', 'Para enviar una órden de compras debe haber como máximo un proveedor!');
          return redirect(url()->previous());
        }
      }else {
        Session::flash('message.level', 'danger');
        Session::flash('status', 'Para generar una compra debe haber un proveedor definido!');
        return redirect(url()->previous());
      }
      if(!$suppliers) return  redirect('neutrinus/error/400');
      $next_purchase_number = Purchase::max('order_number')+1;
      foreach($suppliers as $key => $supplier)
      {
        $purchase = new Purchase();
        $purchase->order_number = $next_purchase_number;
        $purchase->supplier_id = $key;
        $purchase->emitted_date = Carbon::now();
        $purchase->requested_delivery_date = Carbon::now()->addDays(30)->endOfDay(); //Esta linea debe cambiarse por la fecha solicitada
        $purchase->effective_delivery_date = null;
        $purchase->observations = 'Observación a definir';
        $purchase->emitter_id = auth()->user()->id;
        $purchase->recipient_id = null;
        $purchase->order_receipt_observations = null;
        $purchase->status = $status;
        $purchase->quotedValue = null;
        $purchase->author_id = auth()->user()->id;
        $purchase->updater_id = auth()->user()->id;
        $purchase->save();

        if($purchase)
        {
          $cartContent = Cart::content();
          Purchase_element::where('purchase_id', '=', $purchase->id)->delete();
          foreach($cartContent as $row)
          {
            $purchase_element = new Purchase_element();
            $purchase_element->purchase_id = $purchase->id;
            $purchase_element->element_id = $row->id;
            $purchase_element->quantity = $row->qty;
            $purchase_element->definition_status = 0;
            $purchase_element->quantity_received = 0;
            $purchase_element->save();
          }
        }
        if($status != 0)
        {
          $this->sendPurchaseOrder($purchase);
        }else{
          $this->generatePurchaseOrder($purchase->id);
        }
      }
      $this->destroyCart();
      return redirect('purchase_orders');
    }

    public function editPurchaseOrder ($id)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return redirect('neutrinus/error/405');

      $purchase = Purchase::findOrFail($id);

      $suppliers = visibleSuppliers();if(!$suppliers) return redirect('neutrinus/error/405');
      $suppliers_data = $suppliers;
      $suppliers = $suppliers->pluck('name', 'id');

      return view('purchases.edit')->withPurchase($purchase)->withSuppliers($suppliers);
    }

    public function upgradePurchase (request $request)
    {
      $purchase = Purchase::findOrFail($request->id);
      $allowed = true;
      $goTo = false;
      if($request->status != 6){
        switch($purchase->status)
        {
          case 0: //Solicitar cotizacion
            if(auth()->user()->permissionCreatePurchase_Order->state) {$purchase->status = $purchase->status+1;}else{$allowed = false;}
          break;
          case 1: //Cotización recibida
            if(auth()->user()->permissionCreatePurchase_Order->state) {$purchase->status = $purchase->status+1;}else{$allowed = false;}
          break;
          case 2: //Adjudicar órden de compras
            if(auth()->user()->permissionAwardPurchase_Order->state)
            {
              $purchase->emitter_id = auth()->user()->id;
              $purchase->status = $purchase->status+1;
              $this->sendPurchaseOrder($purchase);
            }else{$allowed = false;}
          break;
          case 3: //Esperando pedido
            if(auth()->user()->permissionReceivePurchase_Order->state) {$purchase->recipient_id = auth()->user()->id; $purchase->status = $purchase->status+1;}else{$allowed = false;}
          break;
          case 4: //Chequear elementos recibidos
            if(auth()->user()->permissionReceivePurchase_Order->state) {$goTo = $purchase->id;}else{$allowed = false;}
          break;
          case 5: //Órden cerrada
            if(auth()->user()->permissionDeletePurchase_Order->state) {$purchase->status = $purchase->status+1;}else{$allowed = false;}
          break;
          case 6: //Proceso Anulado
            if(auth()->user()->permissionCreatePurchase_Order->state) {$purchase->status = 0;}else{$allowed = false;}
          break;
        }
      }
      if($request->status == 6 AND (auth()->user()->permissionDeletePurchase_Order->state OR $purchase->status<3)) {$purchase->status = 6; $allowed = true;}
      if($request->quotation AND auth()->user()->permissionCreatePurchase_Order->state) $purchase->quotedValue = $request->quotation;
      $purchase->save();
      return [$purchase->statusName, 'btn btn-sm '.$purchase->statusBtnClass, $purchase->status, $purchase->nextStatusName, $purchase->id, $purchase->orderName, $allowed, $goTo];

      if(!auth()->user()->permissionCreatePurchase_Order->state) return redirect('neutrinus/error/405');
    }

    public function deleteForEverPurchase(Request $request)
    {
        if(!auth()->user()->permissionDeletePurchase_order->state)
        {
          return redirect('neutrinus/error/405');
        }
        Purchase::findOrFail($request->id)->delete();
        return redirect('/purchase_orders');

    }

    public function addToShoppingCart(request $request)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return['Acceso no permitido', ''];

      $element = Element::findOrFail($request->id);
      $elementTotalMaterialCost = $element->materialCost[0] + $element->additional_material_cost;
      Cart::add($element->id, $element->name, $request->qty, $elementTotalMaterialCost, ['id' => $request->id]);
      $return[0] = 'Se agregó '.$element->name.' al carro de compras satisfactoriamente. (Cantidad x'. $request->qty .')<span class="float-right cursor-pointer" onclick="$(\'#alertBox\').fadeOut(\'fast\');">X</span>';
      $return[1] = Cart::count();
      return $return;
    }

    public function updateFromShoppingCart(request $request)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return['Acceso no permitido', '', '', '', '', '', '', '', ''];

      $element = Element::findOrFail($request->id);
      Cart::update($request->rowId, $request->qty);
      $row = Cart::get($request->rowId);
      $return[0] = 'Se cambió a '.$request->qty.' la cantidad de '.$element->name.'.<span class="float-right cursor-pointer" onclick="$(\'#alertBox\').fadeOut(\'fast\');">X</span>';
      $return[1] = Cart::count();
      $return[2] = Cart::subtotal();
      $return[3] = Cart::tax();
      $return[4] = Cart::total();
      $return[5] = round($row->subtotal, 2);
      $return[6] = round($row->tax*$row->qty, 2);
      $return[7] = round($row->total, 2);
      $return[8] = $row->qty;
      return $return;
    }

    public function removeFromShoppingCart(request $request)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return['Acceso no permitido', '', '', '', ''];

      $element = Element::findOrFail($request->id);
      Cart::remove($request->rowId);
      $return[0] = 'Se quitó '.$element->name.' del carro de compras satisfactoriamente.<span class="float-right cursor-pointer" onclick="$(\'#alertBox\').fadeOut(\'fast\');">X</span>';
      $return[1] = Cart::count();
      $return[2] = Cart::subtotal();
      $return[3] = Cart::tax();
      $return[4] = Cart::total();
      return $return;
    }

    public function showShoppingCart()
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return redirect('neutrinus/error/405');

      $cartSuppliers = Array();
      if(session()->has('cartSuppliers')) $cartSuppliers = session()->get('cartSuppliers');
      return view('shopping_cart')->withCartSuppliers($cartSuppliers);
    }

    public function destroyCart()
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return redirect('neutrinus/error/405');

      Cart::destroy();
      session()->forget('cartSuppliers');
      return redirect(url()->previous());
    }

    public function addSupplierToShoppingCart(request $request)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return 'Acceso no permitido';

      $supplier = Supplier::findOrFail($request->id);
      $cartSuppliers = $request->session()->get('cartSuppliers',[]);
      $cartSuppliers[$supplier->id] = $supplier->name;
      $request->session()->put('cartSuppliers', $cartSuppliers);

      return 'Se a agregado '.$supplier->name.' a la lista de proveedores de la órden';
    }

    public function removeSupplierFromShoppingCart(request $request)
    {
      if(!auth()->user()->permissionCreatePurchase_Order->state) return 'Acceso no permitido';

      session()->forget('cartSuppliers.'.$request->id);
      return 'Se a removido el proveedor de la lista de proveedores de la órden';
    }

    private function sendPurchaseOrder($purchase)
    {
      $this->generatePurchaseOrder($purchase->id);

      $subject = 'PEDIDO '.$purchase->orderName;
      $message = 'Estimados, les escribo para solicitar formalmente el material indicado en la órden de compras '.$purchase->orderName.' ajunta a este correo. Quedo a la espera de su pronta respuesta.';
      $attachment[0] = storage_path('app').'/files/purchaseOrders/'.$purchase->id.'.pdf';
      $attachment[1] = $purchase->orderName.'.pdf';
      $to[0] = $purchase->supplier->email;
      app('App\Http\Controllers\MailController')->sendMail($to, $subject, $message, $attachment);
    }

    private function generatePurchaseOrder($id)
    {
      $purchase = Purchase::findOrFail($id);
      $pageNumber = 1;
      $pdf = new Fpdi();
      $pdf->SetAuthor('Neutrinus');
      $pdf->SetCreator('Neutrinus');
      $pdf->SetTitle($purchase->orderName);
      $this->newPagePurchaseOrder($pdf, $purchase, $pageNumber);
      $textHeight = 5;
      $mW1 = 10;
      $mW2 = 30;
      $mW3 = 35;
      $mW4 = 40;
      $mW5 = 10;
      $mW6 = 20;
      $mW7 = 20;
      $mW8 = 10;

      //Incluyo títulos para la tabla de elementos
      $yTableElementsPosition = 59;
      $this->printTableElementsTitles($pdf, $yTableElementsPosition, $mW1, $mW2, $mW3, $mW4, $mW5, $mW6, $mW7, $mW8);
      //Incluyo tabla de elementos
      $item = 0;
      $lineNumber = 0;
      //agrupo elementos con mismo material y tipo de pedido
      $groupedElements = array();
      $groupedElements = $this->GroupTableElements($pdf, $purchase, $groupedElements); //[0:shared_material, 1:[array(id)], 2:array(quantity), 3:material_id, 4:tipo_pedido_id, 5:d_ext, 6:d_int, 7:lado_a, 8:lado_b, 9:largo, 10:ancho, 11:espesor, 12:totalQuantity]
      //fin de agrupacion de elementos

      foreach($groupedElements as $element)
      {
        //GENERO ELEMENTO DE REFERENCIA PARA PODER USAR LOS METODOS DEL MODELO
        $first_element = Element::findOrFail($element[1][0]);

        //MODIFICO LAS DIMENSIONES A IMPRIMIR
        $dimensions = $first_element->dimensions;
        $dimensions = explode("x", $dimensions);
        array_pop($dimensions);
        $dimensions = implode("x", $dimensions);
        if(floatval($element[9]) != 0) $dimensions .= "x".$element[9];
        if($dimensions != "") $dimensions .= "mm";

        $item++;
        $lineNumber++;
        $maxNumberOfLines = 0;
        if(($yTableElementsPosition+$lineNumber*$textHeight)>270) //Si supero una cantidad de renglones inserto una nueva página
        {
          $pageNumber++;
          $this->newPagePurchaseOrder($pdf, $purchase, $pageNumber);
          $lineNumber = 0;

          //Incluyo títulos para la tabla de elementos
          $yTableElementsPosition = 15;
          $this->printTableElementsTitles($pdf, ($yTableElementsPosition-$textHeight), $mW1, $mW2, $mW3, $mW4, $mW5, $mW6, $mW7, $mW8);
        }

        // Incluyo línea inferior de renglon
        $pdf->SetDrawColor(200,200,200);
        $pdf->Line(15, $yTableElementsPosition+$lineNumber*$textHeight, 200, $yTableElementsPosition+$lineNumber*$textHeight);
        if ($lineNumber != 1) $pdf->Line(15, $yTableElementsPosition+$lineNumber*$textHeight-$textHeight/4, 15, $yTableElementsPosition+$lineNumber*$textHeight+$textHeight/4);

        //Incluyo el número de item
        $text = utf8_decode($item);
        $maxWidth = $mW1;
        $fontSize = 10;
        $xPos = 18;
        $textHeight = 5;
        $multiLine = false;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo el tipo de pedido
        $text = utf8_decode($first_element->order_type->name);
        $maxWidth = $mW2;
        $fontSize = 10;
        $xPos = $xPos + $mW1;
        $textHeight = 5;
        $multiLine = true;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo el material
        $text = utf8_decode($first_element->material->name);
        $maxWidth = $mW3;
        $fontSize = 10;
        $xPos = $xPos + $mW2;
        $textHeight = 5;
        $multiLine = true;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo las medidas del elemento
        $text = utf8_decode($dimensions);
        $maxWidth = $mW4;
        $fontSize = 10;
        $xPos = $xPos + $mW3;
        $textHeight = 5;
        $multiLine = true;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo la cantidad a pedir del elemento
        $text = utf8_decode($element[12]);
        $maxWidth = $mW5;
        $fontSize = 10;
        $xPos = $xPos + $mW4;
        $textHeight = 5;
        $multiLine = false;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo códigos y cantidades internas
        $text = array();
        foreach($element[1] as $key => $id)
        {
          $el=Element::find($id);
          array_push($text ,$el->nro."-".$el->add."(".$element[2][$key].")");
        }
        $text = implode(" ", $text);
        $text = utf8_decode($text);
        $maxWidth = $mW6;
        $fontSize = 10;
        $xPos = $xPos + $mW5;
        $textHeight = 5;
        $multiLine = true;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        //Incluyo códigos de proveedor
        $text = array();
        foreach($element[1] as $key => $id)
        {
          $el=Element::find($id);
          foreach($el->supplier_code as $supplier_code)
          {
            if($supplier_code->supplier_id == $purchase->supplier->id & $supplier_code->state_id == 1)
            {
              array_push($text , $supplier_code->code);
            }
          }
        }
        $text = implode(" ", $text);
        $text = utf8_decode($text);
        $maxWidth = $mW7;
        $fontSize = 10;
        $xPos = $xPos + $mW6;
        $textHeight = 5;
        $multiLine = true;
        $yPos = ($yTableElementsPosition+$lineNumber*$textHeight);
        $justify = 'L'; //L = Left, C = Center , R = Right
        $numberOfLines = $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine);
        if($numberOfLines > $maxNumberOfLines) $maxNumberOfLines = $numberOfLines;

        if($maxNumberOfLines > 1) $lineNumber = $lineNumber + $maxNumberOfLines - 1;


      }

      // Incluyo línea inferior para autor y página
      $pdf->SetDrawColor(100,100,100);
      if($yPos < 270)  $pdf->Line(15, ($yPos+1.5*$textHeight), 200, 275);


      $pdf -> Output(storage_path('app').'/files/purchaseOrders/'.$purchase->id.'.pdf' ,'F');
      return;
    }

    private function newPagePurchaseOrder($pdf, $purchase, $pageNumber)
    {
      $pdf -> AddPage('P', array(210, 297));
      $pdf -> SetAutoPageBreak('off', '0');
      $pdf -> SetMargins('0', '0', '0');
      $pdf -> SetFont('Arial');
      $pdf -> SetFontSize('11');
      $pdf -> SetTextColor(0, 0, 0);

      if($pageNumber == 1) //Solo para la primera página
      {
        //Incluyo fecha de emisión de la órden arriba a la derecha
        $text = utf8_decode('Fecha de emisión: '.date('d/m/Y', strtotime($purchase->emitted_date)));
        $maxWidth = 100;
        $fontSize = 10;
        $xPos = (210-13);
        $yPos = 7;
        $textHeight = 5;
        $justify = 'R'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Título principal
        $text = utf8_decode($purchase->orderType.' '.$purchase->orderName);
        $maxWidth = 195;
        $fontSize = 18;
        $xPos = 18;
        $yPos = 17;
        $textHeight = 6;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        $yTablePosition = 34; //Posición en Y para la tabla de datos de empresa y proveedor

        // Incluyo línea inferior para autor y página
        $pdf->Line(15, $yTablePosition-9, 200, $yTablePosition-9);

        //Incluyo nombre de empresa bajo el título
        $text = utf8_decode('Emite: '.config('constants.company_name'));
        $maxWidth = 90;
        $fontSize = 10;
        $xPos = 18;
        $yPos = $yTablePosition-7;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo teléfono de empresa bajo el título
        $text = utf8_decode('Teléfono: '.config('constants.company_phone_number'));
        $maxWidth = 90;
        $fontSize = 10;
        $xPos = 18;
        $yPos = $yTablePosition-2;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo dirección de empresa bajo el título
        $text = utf8_decode('Dirección: '.config('constants.company_address'));
        $maxWidth = 95;
        $fontSize = 10;
        $xPos = (105-13);
        $yPos = $yTablePosition-7;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo fecha de entrega solicitada
        $text = utf8_decode('Fecha de entrega solicitada: '.date('d/m/Y', strtotime($purchase->requested_delivery_date)));
        $maxWidth = 95;
        $fontSize = 10;
        $xPos = (105-13);
        $yPos = $yTablePosition-2;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        // Incluyo línea inferior para autor y página
        $pdf->Line(15, $yTablePosition+4, 200, $yTablePosition+4);

        //Incluyo Proveedor
        $text = utf8_decode('Para: '.$purchase->supplier->name);
        $maxWidth = 90;
        $fontSize = 10;
        $xPos = 18;
        $yPos = $yTablePosition+5;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Mail del Proveedor
        $text = utf8_decode('Mail: '.$purchase->supplier->email);
        $maxWidth = 90;
        $fontSize = 10;
        $xPos = 18;
        $yPos = $yTablePosition+10;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Telefono del Proveedor
        $text = utf8_decode('Teléfono: '.$purchase->supplier->phone_number);
        $maxWidth = 90;
        $fontSize = 10;
        $xPos = 18;
        $yPos = $yTablePosition+15;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Nombre del contacto del proveedor
        $text = utf8_decode('Contacto: '.$purchase->supplier->contacts->first()->name);
        $maxWidth = 95;
        $fontSize = 10;
        $xPos = (105-13);
        $yPos = $yTablePosition+5;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Mail del contacto del proveedor
        $text = utf8_decode('Mail: '.$purchase->supplier->contacts->first()->email);
        $maxWidth = 95;
        $fontSize = 10;
        $xPos = (105-13);
        $yPos = $yTablePosition+10;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        //Incluyo Dirección del Proveedor
        $text = utf8_decode('Dirección: '.$purchase->supplier->completeAddress);
        $maxWidth = 95;
        $fontSize = 10;
        $xPos = (105-13);
        $yPos = $yTablePosition+15;
        $textHeight = 5;
        $justify = 'L'; //L = Left, C = Center , R = Right
        $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

        // Incluyo línea superior divisoria para datos de la órden
        $pdf->Line(15, $yTablePosition+21, 200, $yTablePosition+21);

      }

      // Incluyo línea inferior para autor y página
      $pdf->Line(15, 285, 200, 285);

      //Incluyo etiqueta de autor
      $text = utf8_decode('Emisor: '.$purchase->emitter->name);
      $maxWidth = 100;
      $fontSize = 10;
      $xPos = (210-13);
      $yPos = 287;
      $textHeight = 5;
      $justify = 'R'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo etiqueta de numero de página
      $text = utf8_decode('Página '.$pageNumber.' - '.$purchase->orderName);
      $maxWidth = 95;
      $fontSize = 10;
      $xPos = 18;
      $yPos = 287;
      $textHeight = 5;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      return $pdf;
    }

    private function writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify, $multiLine = false)
    {
      $pdf->SetFontSize($fontSize);
      if($justify == 'C') $xPos = $xPos-(($pdf->GetStringWidth($text)+2.5)/2);
      if($justify == 'R') $xPos = $xPos-($pdf->GetStringWidth($text)+2.5);
      $numberOfLines = 0;
      if($multiLine)
      {
        $remainingText = explode(" ", $text);
        $lineText = '';
        foreach($remainingText as $key => $word)
        {
          if($pdf->GetStringWidth($lineText.' '.$word) <= $maxWidth)
          {
            $lineText = $lineText.' '.$word;
          }else{
            if($pdf->GetStringWidth($word) <= $maxWidth)
            {
              $pdf -> SetXY($xPos, $yPos);
              $pdf -> Write($textHeight, $lineText);
              $yPos = $yPos + $textHeight;
              $numberOfLines++;
              $lineText = $word;
            }else{
              while ($pdf->GetStringWidth($word) > $maxWidth) {
                 //$fontSize--;
                 //$pdf->SetFontSize($fontSize);
                 $word = substr($word, 0, -1);
              }
              $word = substr($word, 0, -2);
              $word .= '..';
              if ($lineText != '')
              {
                $pdf -> SetXY($xPos, $yPos);
                $pdf -> Write($textHeight, $lineText);
                $yPos = $yPos + $textHeight;
                $numberOfLines++;
              }
              $pdf -> SetXY($xPos, $yPos);
              $pdf -> Write($textHeight, $word);
              $yPos = $yPos + $textHeight;
              $numberOfLines++;
              $lineText = '';
            }
          }
        }
        while ($pdf->GetStringWidth($lineText) > $maxWidth) {
           $fontSize--;
           $pdf->SetFontSize($fontSize);
        }
        if ($lineText != '')
        {
          $pdf -> SetXY($xPos, $yPos);
          $pdf -> Write($textHeight, $lineText);
          $yPos = $yPos + $textHeight;
          $numberOfLines++;
        }

        /*while(strlen($remainingText) > 0)
        {
          $lineText = $remainingText;
          while (($pdf->GetStringWidth($lineText) > $maxWidth)) {
             $lineText = substr($lineText, 0, -1);
          }
          $pdf -> SetXY($xPos, $yPos);
          $pdf -> Write($textHeight, $lineText);
          $yPos = $yPos + $textHeight;
          $numberOfLines++;
          $remainingText = substr($remainingText, strlen($lineText));
        }*/
      }else{
        while ($pdf->GetStringWidth($text) > $maxWidth) {
           $fontSize--;
           $pdf->SetFontSize($fontSize);
        }
        $pdf -> SetXY($xPos, $yPos);
        $pdf -> Write($textHeight, $text);
        $numberOfLines++;
      }
      return $numberOfLines;
    }

    private function printTableElementsTitles($pdf, $yTableElementsPosition, $mW1, $mW2, $mW3, $mW4, $mW5, $mW6, $mW7, $mW8)
    {
      //Incluyo el título del número de item
      $text = utf8_decode('Item');
      $maxWidth = $mW1;
      $fontSize = 10;
      $xPos = 18;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título del tipo de pedido
      $text = utf8_decode('Formato');
      $maxWidth = $mW2;
      $fontSize = 10;
      $xPos = $xPos + $mW1;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título del material
      $text = utf8_decode('Material');
      $maxWidth = $mW3;
      $fontSize = 10;
      $xPos = $xPos + $mW2;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título de las medidas del elemento
      $text = utf8_decode('Medidas');
      $maxWidth = $mW4;
      $fontSize = 10;
      $xPos = $xPos + $mW3;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título de la cantidad a pedir del elemento
      $text = utf8_decode('Cant.');
      $maxWidth = $mW5;
      $fontSize = 10;
      $xPos = $xPos + $mW4;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título de los codigos internos
      $text = utf8_decode('Elemento');
      $maxWidth = $mW6;
      $fontSize = 10;
      $xPos = $xPos + $mW5;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título de codigo de proveedor
      $text = utf8_decode('Código');
      $maxWidth = $mW7;
      $fontSize = 10;
      $xPos = $xPos + $mW6;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      //Incluyo el título de elementos recibidos
      $text = utf8_decode('Estado');
      $maxWidth = $mW8;
      $fontSize = 10;
      $xPos = $xPos + $mW7;
      $textHeight = 5;
      $yPos = $yTableElementsPosition;
      $justify = 'L'; //L = Left, C = Center , R = Right
      $this->writeTextPdf($pdf, $text, $maxWidth, $fontSize, $xPos, $yPos, $textHeight, $justify);

      return;
    }

    private function GroupTableElements($pdf, $purchase, $groupedElements)
    {
      //Cada elemento será un array de [0:shared_material, 1:[array(id)], 2:array(quantity), 3:material_id, 4:tipo_pedido_id, 5:d_ext, 6:d_int, 7:lado_a, 8:lado_b, 9:largo, 10:ancho, 11:espesor, 12:totalQuantity]
      foreach($purchase->elements as $elementFromOrder){
        //var_dump($elementFromOrder->quantity);var_dump($elementFromOrder->element);die();

        if(!$elementFromOrder->element->shared_material)
        {
          //1ro verifico si hay más de una vez cargado el mismo elemento y lo unifico
          $found = false;
          foreach($groupedElements as $key => $searchingElement)
          {
            if (in_array($elementFromOrder->element_id, $searchingElement[1]))
            {
              $found = true;
              $groupedElements[$key][2][0] += $elementFromOrder->quantity;
              $groupedElements[$key][12] += $elementFromOrder->quantity;
              break;
            }
          }
          if (!$found)
          {
            $newElement = [$elementFromOrder->element->shared_material, array($elementFromOrder->element_id), array($elementFromOrder->quantity), $elementFromOrder->element->material_id, $elementFromOrder->element->order_type_id, $elementFromOrder->element->d_ext, $elementFromOrder->element->d_int, $elementFromOrder->element->side_a, $elementFromOrder->element->side_b, $elementFromOrder->element->large, $elementFromOrder->element->width, $elementFromOrder->element->thickness, $elementFromOrder->quantity];
            array_push($groupedElements, $newElement);
          }
          //fin de verificacion de elementos cargados más de una vez
        }else{
          $found = false;
          foreach($groupedElements as $key => $searchingElement)
          {
            if(
              $searchingElement[3] == $elementFromOrder->element->material_id AND
              $searchingElement[4] == $elementFromOrder->element->order_type_id AND
              $searchingElement[5] == $elementFromOrder->element->d_ext AND
              $searchingElement[6] == $elementFromOrder->element->d_int AND
              $searchingElement[7] == $elementFromOrder->element->side_a AND
              $searchingElement[8] == $elementFromOrder->element->side_b AND
              $searchingElement[10] == $elementFromOrder->element->width AND
              $searchingElement[11] == $elementFromOrder->element->thickness
              )
            {
              $found = true;
              array_push($groupedElements[$key][1], $elementFromOrder->element_id);
              array_push($groupedElements[$key][2], $elementFromOrder->quantity);
              $groupedElements[$key][9] += ($elementFromOrder->element->large*$elementFromOrder->quantity);
              $groupedElements[$key][12] = 1;
              break;
            }
          }
          if (!$found)
          {
            $newElement = [$elementFromOrder->element->shared_material, array($elementFromOrder->element_id), array($elementFromOrder->quantity), $elementFromOrder->element->material_id, $elementFromOrder->element->order_type_id, $elementFromOrder->element->d_ext, $elementFromOrder->element->d_int, $elementFromOrder->element->side_a, $elementFromOrder->element->side_b, ($elementFromOrder->element->large*$elementFromOrder->quantity), $elementFromOrder->element->width, $elementFromOrder->element->thickness, 1];
            array_push($groupedElements, $newElement);
          }
        }
      }
      return $groupedElements;
    }

    public function showPdf($id)
    {
      $purchase = Purchase::findOrFail($id);
      if(!auth()->user()->permissionViewPurchase_Orders->state OR !auth()->user()->permissionViewPurchase_OrderPrices->state)
      {
        return redirect('neutrinus/error/405');
      }
      $pdf = new Fpdi();
      $pageCount = $pdf->setSourceFile(storage_path('app').'/files/purchaseOrders/'.$purchase->id.'.pdf');
      for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $tplIdx = $pdf -> importPage($pageNo);
        $size = $pdf->getTemplateSize($tplIdx);
        $pdf -> AddPage();
        $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'],FALSE);
      }
      $pdf-> SetTitle(utf8_decode($purchase->orderType).' - '.$purchase->orderName);
      $pdf -> Output('I', $purchase->orderName.'.pdf');
    }

}
