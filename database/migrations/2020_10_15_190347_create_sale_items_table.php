<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('element_id')->nullable();
            $table->integer('subset_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->text('observations')->nullable();
            $table->float('quotedValue')->nullable();
            $table->float('discount')->nullable();
            $table->float('iva_tax_percentaje')->nullable();
            $table->float('other_taxes_percentaje')->nullable();
            $table->float('quantity');
            $table->integer('serial_number')->nullable();
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
        Schema::dropIfExists('sale_items');
    }
}
