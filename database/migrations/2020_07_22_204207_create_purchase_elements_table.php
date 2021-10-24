<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_elements', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id');
            $table->integer('element_id');
            $table->integer('quantity');
            $table->integer('definition_status'); //0 habilitado - 1 deshabilitado - 2 eliminado
            $table->integer('quantity_received');
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
        Schema::dropIfExists('purchase_elements');
    }
}
