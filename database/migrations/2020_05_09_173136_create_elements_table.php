<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elements', function (Blueprint $table) {
          $table->id();
          $table->integer('nro')->index();
          $table->integer('add');
          $table->string('name')->index();
          $table->string('description')->nullable();
          $table->integer('material_id')->index();
          $table->boolean('shared_material'); // Si es true, se puede juntar materiales en la orden de compras, si es false, el material se compra para cada pieza de forma individual. Siempre que se unifican materiales se lo hace a traves de su largo.
          $table->integer('order_type_id')->index();
          $table->float('d_ext');
          $table->float('d_int');
          $table->float('side_a');
          $table->float('side_b');
          $table->float('large');
          $table->float('width');
          $table->float('thickness');
          $table->integer('quantity_per_manufacturing_series'); //Este valor representa la cantidad de piezas que se fabrican en una serie con una unica preparacion de maquina
          $table->integer('general_state_id'); //4 - eliminado
          $table->double('additional_material_cost', 15, 8);
          $table->datetime('additional_material_cost_date')->nullable();
          $table->double('calculated_material_cost', 15, 8);
          $table->datetime('calculated_material_cost_date')->nullable();
          $table->double('sale_price', 15, 8);
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
        Schema::dropIfExists('elements');
    }
}
