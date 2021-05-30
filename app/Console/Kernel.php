<?php

namespace App\Console;

use App\Actions\Tournament\TournamentTreeManager;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $tournaments = Tournament::where('date', Carbon::now()->toDate())->get();
            $tournament_tree_manager = new TournamentTreeManager();
            foreach ($tournaments as $tournament) {
                $tournament_tree_manager->setTournament($tournament);
                $tournament_tree_manager->generateDuels();
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
