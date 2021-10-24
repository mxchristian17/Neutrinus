<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('order_types', function (Blueprint $table) {
          $table->id();
          $table->string('name');
          $table->string('description');
          $table->boolean('d_ext');
          $table->boolean('d_int');
          $table->boolean('side_a');
          $table->boolean('side_b');
          $table->boolean('large');
          $table->boolean('width');
          $table->boolean('thickness');
          $table->string('formula');
          $table->string('original_formula');
          $table->integer('state_id'); //1 - Habilitado, 2 - Deshabilitado, 3 - Oculto, 4 - Eliminado
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
        Schema::dropIfExists('order_types');
    }
}
