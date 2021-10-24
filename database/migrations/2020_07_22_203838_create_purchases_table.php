<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('order_number');
            $table->integer('supplier_id');
            $table->datetime('emitted_date');
            $table->datetime('requested_delivery_date')->nullable();
            $table->datetime('effective_delivery_date')->nullable();
            $table->text('observations')->nullable(); //observaciones a incluir en la orden
            $table->integer('emitter_id')->nullable(); //emisor de la orden
            $table->integer('recipient_id')->nullable(); //receptor de la orden
            $table->text('order_receipt_observations')->nullable();
            $table->integer('status'); // 0 solicitar cotizacion - 1 esperando cotización - 2 enviar orden de compras - 3 esperando pedido - 4 chequear elementos recibidos - 5 orden cerrada - 6 orden anulada
            $table->float('quotedValue')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
