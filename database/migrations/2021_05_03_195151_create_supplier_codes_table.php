<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_codes', function (Blueprint $table) {
            $table->id();
            $table->integer("element_id");
            $table->integer("supplier_id");
            $table->integer('state_id'); //1 - Habilitado, 2 - Deshabilitado, 3 - Oculto, 4 - Eliminado
            $table->text("description")->nullable();
            $table->text("code");
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
        Schema::dropIfExists('supplier_codes');
    }
}
