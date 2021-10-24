<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->integer("provider_id");
            $table->integer('state_id'); //1 - Habilitado, 2 - Deshabilitado, 3 - Oculto, 4 - Eliminado
            $table->text("description")->nullable();
            $table->integer("currency_id");
            $table->float("tax_amount");
            $table->datetime("due_date");
            $table->integer("status")->nullable(); //0 - impago, 1- pago
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
        Schema::dropIfExists('taxes');
    }
}
