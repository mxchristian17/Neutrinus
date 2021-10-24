<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('permissions');
      Schema::create('permissions', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->integer('code_id');
        $table->boolean('state');
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
        //
    }
}
