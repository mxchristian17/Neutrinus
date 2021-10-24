<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('material_id');
            $table->integer('order_type_id');
            $table->float('d_ext');
            $table->float('d_int');
            $table->float('side_a');
            $table->float('side_b');
            $table->float('width');
            $table->float('thickness');
            $table->float('price');
            $table->boolean('enabled');
            $table->integer('supplier_id');
            $table->integer("author_id");
            $table->integer("updater_id");
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
        Schema::dropIfExists('material_prices');
    }
}
