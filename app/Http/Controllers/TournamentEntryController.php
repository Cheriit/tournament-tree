<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TournamentEntryController extends Controller
{
    public  function store(Request $request) {
        $validate = $request->validate(TournamentEntry::$validation_rules);
        $tournament = Tournament::findOrFail($validate['tournament_id']);

        $request->validate([
            'licence_number' => [
                Rule::unique('tournament_entries')->where('tournament_id', $tournament->id)
            ],
            'current_ranking' => [
                Rule::unique('tournament_entries')->where('tournament_id', $tournament->id)
            ]
        ]);

        if (Auth::id() != $tournament->organisator_id) {
            $validate['user_id'] = Auth::id();

            if (
                $tournament->max_participants > $tournament->entries()->count()
                && Carbon::parse($tournament->date)->toDate() > Carbon::now()->toDate()) {
                if (TournamentEntry::where('user_id', Auth::id())->where('tournament_id', $tournament->id)->count() == 0) {
                    TournamentEntry::create($validate);
                    $request->session()->flash('flash.banner', 'You\'ve been enrolled to tournament');
                    $request->session()->flash('flash.bannerStyle', 'success');
                } else {
                    $request->session()->flash('flash.banner', 'You\'re already enrolled to this tournament');
                    $request->session()->flash('flash.bannerStyle', 'danger');
                }
            } else {
                $request->session()->flash('flash.banner', 'Too big number of participants or it\'s too late to enroll');
                $request->session()->flash('flash.bannerStyle', 'danger');
            }
        } else {
            $request->session()->flash('flash.banner', 'You cannot participate in your own tournament');
            $request->session()->flash('flash.bannerStyle', 'danger');
        }

        return Redirect::back();
    }

    public  function destroy(Request $request) {
        //
    }
}
