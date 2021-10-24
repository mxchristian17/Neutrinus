<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('sales', function (Blueprint $table) {
          $table->id();
          $table->string('order_number')->nullable();
          $table->integer('client_id');
          $table->text('observations')->nullable(); //observaciones a incluir en la orden
          $table->integer('work_order_emitter_id')->nullable(); //emisor de la orden de trabajo
          $table->text('work_order_observations')->nullable();
          $table->integer('status'); // 1 Cotizar - 2 cotizado - 3 OC recibida - 4 Facturado - 5 Listo para entregar - 6 Entregado - 7 orden cerrada - 8 orden anulada
          $table->integer('currency_id');
          $table->string('bill_number')->nullable(); //Número de factura
          $table->float('retentions')->nullable();
          $table->float('perceptions')->nullable();
          //$table->float('discount')->nullable();
          $table->datetime('requested_delivery_date');
          $table->datetime('scheduled_delivery_date')->nullable();
          $table->datetime('quote_request_date')->nullable();
          $table->datetime('quote_date')->nullable();
          $table->datetime('purchase_order_reception_date')->nullable();
          $table->datetime('ready_to_deliver_date')->nullable();
          $table->datetime('delivered_date')->nullable();
          $table->integer('state_id');
          $table->integer('author_id');
          $table->integer('updater_id');
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
