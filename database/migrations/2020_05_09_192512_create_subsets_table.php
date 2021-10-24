<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subsets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('project_id');
            $table->integer('subset_number');
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
        Schema::dropIfExists('subsets');
    }
}
