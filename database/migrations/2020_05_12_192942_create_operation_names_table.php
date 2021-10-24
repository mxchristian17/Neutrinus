<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
      			$table->string('description');
            $table->float('usd_for_hour');
            $table->integer('state_id');
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
        Schema::dropIfExists('operation_names');
    }
}
