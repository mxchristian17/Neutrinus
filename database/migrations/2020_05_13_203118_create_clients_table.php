<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string("name");
        $table->integer("state_id"); //1 - Habilitado, 2 - Deshabilitado, 3 - Oculto, 4 - Eliminado
        $table->string("phone_number")->nullable();
        $table->string("email")->unique()->nullable();
        $table->string("cuit")->nullable();
        $table->integer("taxpayer_type_id"); //1-monotributista - 2-responsable inscripto
        $table->string("address")->nullable();
        $table->string("city")->nullable();
        $table->string("province")->nullable();
        $table->string("country")->nullable();
        $table->integer("currency_id");
        $table->string("description")->nullable();
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
        Schema::dropIfExists('clients');
    }
}
