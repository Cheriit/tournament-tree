<?php


namespace App\Actions\Tournament;


use App\Models\Tournament;
use App\Models\TournamentDuel;
use App\Models\TournamentEntry;
use Illuminate\Database\Eloquent\Model;

class TournamentTreeManager
{
    private Tournament $tournament;

    /**
     * @param Tournament $tournament
     */
    public function setTournament(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    function generateDuels() {
        if ($this->tournament->current_stage <= $this->tournament->max_stage_number )
            switch ($this->tournament->current_stage) {
                case 0:
                    $this->generateBaseTree();
                    break;
                case $this->tournament->max_stage_number:
                    $this->finishTournament();
                    break;
                default:
                    $this->generateNextLevelTree();
                    break;
            }
    }

    private function generateBaseTree() {
        $entries = $this->tournament->entries()->orderBy('current_ranking', 'DESC');
        if ($entries->count() === 1) {
            $entries[0]->current_position = 1;
            $entries[0]->save();
        } else {
            $duel = null;
            foreach ($entries->get() as $id=>$entry) {
                if ($id%2==0) {
                    $duel = new TournamentDuel();
                    $duel->tournament_id = $this->tournament->id;
                    $duel->first_user_entry_id = $entry->id;
                    $duel->second_user_entry_id = null;
                    $duel->first_player_win = true;
                    $duel->second_player_win = false;
                    $duel->stage = 1;
                } else {
                    $duel->second_user_entry_id = $entry->id;
                    $duel->first_player_win = null;
                    $duel->second_player_win = null;
                }
                $duel->save();
            }
            $this->tournament->current_stage = 1;
            $this->tournament->max_stage_number = ceil(log($entries->count(), 2));
            $this->tournament->save();
        }
    }

    private function getUnfilledDuelsCount() {
        return TournamentDuel::where('tournament_id', $this->tournament->id)
            ->where('stage', $this->tournament->current_stage)
            ->where(function ($query) {
                $query->whereNull('first_player_win')
                    ->orWhereNull('second_player_win');
            })->count();

    }

    private function generateNextLevelTree()
    {
        $unfilled_collections_count = $this->getUnfilledDuelsCount();
        $new_stage = $this->tournament->current_stage + 1;
        $stage_position =  $this->getStagePosition();
        if ($unfilled_collections_count == 0) {
            $duels = TournamentDuel::where('tournament_id', $this->tournament->id)
                ->where('stage', $this->tournament->current_stage)
                ->with('first_entry', 'second_entry')
                ->get();
            $duel = null;
            foreach ($duels as $id => $past_duel) {
                if ($id % 2 == 0) {
                    $duel = new TournamentDuel();
                    $duel->tournament_id = $this->tournament->id;
                    $duel->first_user_entry_id =  $past_duel->first_player_win ? $past_duel->first_user_entry_id : $past_duel->second_user_entry_id ;
                    $this->updateDuelPositions($past_duel, $stage_position);
                    $duel->second_user_entry_id = null;
                    $duel->first_player_win = true;
                    $duel->second_player_win = false;
                    $duel->stage = $new_stage;
                } else {
                    $this->updateDuelPositions($past_duel, $stage_position);
                    $duel->second_user_entry_id =  $past_duel->first_player_win ? $past_duel->first_user_entry_id : $past_duel->second_user_entry_id ;
                    $duel->first_player_win = null;
                    $duel->second_player_win = null;

                }
                $duel->save();
            }
            $this->tournament->current_stage = $new_stage;
            $this->tournament->save();
        }
    }

    private function updateDuelPositions($duel, $stage_position) {
        if ($duel->first_player_win) {
            if ($duel->second_entry != null) {
                $entry = $duel->second_entry;
                $entry->current_position = $stage_position;
                $entry->save();
            }
        } else {
            $entry = $duel->first_entry;
            $entry->current_position = $stage_position;
            $entry->save();
        }

    }
    private function finishTournament()
    {
        $unfilled_collections_count = $this->getUnfilledDuelsCount();
        if ($unfilled_collections_count == 0) {
            $duel = TournamentDuel::where('tournament_id', $this->tournament->id)
                ->where('stage', $this->tournament->current_stage)
                ->with('first_entry', 'second_entry')
                ->first();
            if ($duel->first_player_win) {
                $duel->second_entry->current_position = 2;
                $duel->first_entry->current_position = 1;
            } else {
                $duel->second_entry->current_position = 1;
                $duel->first_entry->current_position = 2;
            }
            $duel->second_entry->save();
            $duel->first_entry->save();
            $this->tournament->current_stage++;
            $this->tournament->save();
        }
    }

    private function getStagePosition() {
        return pow(2, $this->tournament->max_stage_number - $this->tournament->current_stage) + 1;
    }
}
