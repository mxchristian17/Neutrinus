<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('sale_id')->nullable();
            $table->integer('element_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->datetime('task_start');
            $table->datetime('task_estimated_end');
            $table->boolean('activated');
            $table->integer('percentage');
            $table->boolean('showed');
            $table->boolean('repeat');
            $table->boolean('repeat_days_interval')->nullable();
            $table->string('title');
            $table->text('content')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
