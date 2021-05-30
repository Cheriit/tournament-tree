<?php

namespace App\Http\Controllers;

use App\Actions\Tournament\TournamentTreeManager;
use App\Models\Tournament;
use App\Models\TournamentDuel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class TournamentDuelController extends Controller
{
    public function index(Request $request): Response
    {
        $unfinished_duels = DB::select(
            "SELECT duel.id, tournament.id AS tournament_id, opponent.name AS opponent_name, tournament.name, tournament.date, duel.created_at
                    FROM tournament_duels duel
                        INNER JOIN tournament_entries entry
                            ON duel.first_user_entry_id = entry.id
                        INNER JOIN tournaments tournament
                            ON entry.tournament_id = tournament.id
                        INNER JOIN tournament_entries opponent_entry
                            ON duel.second_user_entry_id = opponent_entry.id
                        INNER JOIN users opponent
                            ON opponent.id = opponent_entry.user_id
                    WHERE
                        entry.user_id = ? and
                        first_player_win IS NULL
                    UNION

                    SELECT duel.id, tournament.id AS tournament_id, opponent.name AS opponent_name, tournament.name, tournament.date, duel.created_at
                    FROM tournament_duels duel
                        INNER JOIN tournament_entries entry
                            ON duel.second_user_entry_id = entry.id
                        INNER JOIN tournaments tournament
                            ON entry.tournament_id = tournament.id
                        INNER JOIN tournament_entries opponent_entry
                            ON duel.first_user_entry_id = opponent_entry.id
                        INNER JOIN users opponent
                            ON opponent.id = opponent_entry.user_id
                    WHERE
                        entry.user_id = ? and
                        duel.second_player_win IS NULL
                    ORDER BY 6;"
                    ,  [Auth::id(), Auth::id()]);

        $finished_duels = DB::select(
            "SELECT duel.id, tournament.id AS tournament_id, opponent.name as opponent_name, tournament.name, tournament.date, duel.first_player_win AS my_result, duel.created_at
                    FROM tournament_duels duel
                        INNER JOIN tournament_entries entry
                            ON duel.first_user_entry_id = entry.id
                        INNER JOIN tournaments tournament
                            ON entry.tournament_id = tournament.id
                        INNER JOIN tournament_entries opponent_entry
                            ON duel.second_user_entry_id = opponent_entry.id
                        INNER JOIN users opponent
                            ON opponent.id = opponent_entry.user_id
                    WHERE
                        entry.user_id = ? and
                        first_player_win IS NOT NULL
                    UNION

                    SELECT duel.id, tournament.id AS tournament_id, opponent.name as opponent_name, tournament.name, tournament.date, duel.second_player_win AS my_result, duel.created_at
                    FROM tournament_duels duel
                        INNER JOIN tournament_entries entry
                            ON duel.second_user_entry_id = entry.id
                        INNER JOIN tournaments tournament
                            ON entry.tournament_id = tournament.id
                        INNER JOIN tournament_entries opponent_entry
                            ON duel.first_user_entry_id = opponent_entry.id
                        INNER JOIN users opponent
                            ON opponent.id = opponent_entry.user_id
                    WHERE
                        entry.user_id = ? and
                        second_player_win IS NOT NULL
                    ORDER BY 7 DESC;"
            , [Auth::id(), Auth::id()]);

        return Inertia::render('Duel/Index', [
            'unfinished_duels' => $unfinished_duels,
            'finished_duels' => $finished_duels
        ]);
    }

    public  function update(Request $request) {
        $validated = $request->validate([
                'id' => [
                    'required',
                    'exists:tournament_duels,id'
                ],
                'state' => [
                    'required',
                    'boolean'
                ]
            ]);
        $duel = TournamentDuel::with('tournament', 'first_entry', 'second_entry')->where('id', $validated['id'])->get()[0];
        if (($duel->first_player_win != null && $duel->second_player_win != null)
            || $duel->second_user_entry_id == null
            || ($duel->first_entry->user_id != Auth::id() && $duel->second_entry->user_id != Auth::id() )
        ) {
            $request->session()->flash('flash.banner', 'You cannot enter results for this tournament');
            $request->session()->flash('flash.bannerStyle', 'danger');
            return Redirect::back();
        }

        if ($duel->first_entry->user_id == Auth::id()) {
            $duel->first_player_win = $validated['state'] == "1";
        } else {
            $duel->second_player_win = $validated['state'] == "1";
        }

        if ($duel->second_player_win !== null && $duel->first_player_win !== null) {
            if ($duel->first_player_win == $duel->second_player_win) {
                $duel->first_player_win = null;
                $duel->second_player_win = null;
                $duel->save();
                $request->session()->flash('flash.banner', 'You and your opponent entered conflicting results. Try again');
                $request->session()->flash('flash.bannerStyle', 'danger');
            } else {
                $duel->save();
                $tournament_manager = new TournamentTreeManager();
                $tournament_manager->setTournament($duel->tournament);
                $tournament_manager->generateDuels();
                $request->session()->flash('flash.banner', 'Your results has been saved');
                $request->session()->flash('flash.bannerStyle', 'success');
            }
        } else {
            $request->session()->flash('flash.banner', 'Your results has been saved');
            $request->session()->flash('flash.bannerStyle', 'success');
            $duel->save();
        }

        return Redirect::back();
    }
}
