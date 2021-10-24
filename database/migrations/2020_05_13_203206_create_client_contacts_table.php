<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('client_contacts', function (Blueprint $table) {
        $table->id();
        $table->integer("client_id");
        $table->string("name");
        $table->string("email")->nullable();
        $table->string("phone_number")->nullable();
        $table->integer("state_id"); //1 - Habilitado, 2 - Deshabilitado, 3 - Oculto, 4 - Eliminado
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
        Schema::dropIfExists('client_contacts');
    }
}
