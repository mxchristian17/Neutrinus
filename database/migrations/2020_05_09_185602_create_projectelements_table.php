<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectelementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projectelements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('element_id')->index();
      			$table->integer('project_id')->index();
            $table->integer('subset_id');
            $table->integer('part');
            $table->integer('subpart');
            $table->integer('version');
            $table->boolean('welded_set');
            $table->integer('quantity');
      			$table->integer('specific_state_id');  //0 - revisar - 1 - Pedir 2 - Pedido - 3 - Produccion - 4 - Finalizado - 5 - Montado - 6 - Corregir - 100 - Eliminado
            $table->integer('purchase_order');
            $table->integer('manufacturing_order');
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
        Schema::dropIfExists('projectelements');
    }
}
