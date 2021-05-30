<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentDuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_duels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('first_user_entry_id');
            $table->unsignedBigInteger('second_user_entry_id')->nullable();
            $table->boolean('first_player_win')->nullable();
            $table->boolean('second_player_win')->nullable();
            $table->integer('stage');
            $table->timestamps();

            $table->foreign('first_user_entry_id')->references('id')->on('tournament_entries');
            $table->foreign('second_user_entry_id')->references('id')->on('tournament_entries');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_duels');
    }
}
