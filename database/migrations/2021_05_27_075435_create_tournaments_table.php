<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('place');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->date('date');
            $table->unsignedInteger('max_participants');
            $table->unsignedInteger('ranked_players');
            $table->unsignedBigInteger('organisator_id');
            $table->unsignedBigInteger('category_id');
            $table->integer('current_stage')->default(0);
            $table->integer('max_stage_number')->default(0);
            $table->timestamps();

            $table->foreign('organisator_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
