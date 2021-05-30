<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tournament;
use App\Models\TournamentEntry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public  function index(Request $request) {
        $search = $request->get('search');
        $category = $request->get('category');
        $tournaments = Tournament::orderBy('name')
            ->filter($search, $category)
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($tournament) => [
                    'id' => $tournament->id,
                    'name' => $tournament->name,
                    'organisator_name' => $tournament->organisator->name,
                    'start_date' => $tournament->date
                ]);

        if (Auth::user() !== null) {
            $organised_tournaments =
                Tournament::where('organisator_id', 'like', Auth::id())
                    ->orderBy('date', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(fn ($tournament) => [
                        'id' => $tournament->id,
                        'name' => $tournament->name,
                        'start_date' => $tournament->date
                    ]);
            $enrolled_tournaments = TournamentEntry::where('user_id', Auth::id())
                ->with(['tournament', 'tournament.organisator'])
                ->limit(10)
                ->get()
                ->map(fn ($tournament_entry) => [
                    'id' => $tournament_entry->tournament->id,
                    'name' => $tournament_entry->tournament->name,
                    'organisator_name' => $tournament_entry->tournament->organisator->name,
                    'start_date' => $tournament_entry->tournament->date
                ]);
        }
        else {
            $organised_tournaments = null;
            $enrolled_tournaments = null;
        }
        return Inertia::render('Tournament/Index', [
            'tournaments' => $tournaments,
            'search' => $search,
            'category' => $category,
            'categories' => Category::all(),
            'organised_tournaments' => $organised_tournaments,
            'enrolled_tournaments' => $enrolled_tournaments
        ]);
    }

    public  function create() {
        $categories = Category::all();
        return Inertia::render('Tournament/Create',[
            'categories' => $categories
        ]);
    }

    public  function store(Request $request): RedirectResponse
    {
        $validate = $request->validate(Tournament::$validation_rules);
        $validate['organisator_id'] = Auth::id();
        Tournament::create($validate);
        $request->session()->flash('flash.banner', 'Tournament has been created');
        $request->session()->flash('flash.bannerStyle', 'success');
        return Redirect::route("tournament.index");
    }

    public  function show(Request $request, $id) {
        $tournament = Tournament::findOrFail($id);
        $is_enrolled = false;
        $is_organisator = false;

        if (Auth::id() != null) {
            $is_enrolled = TournamentEntry::where('user_id', 'like', Auth::id())
                ->where('tournament_id', 'like', $tournament->id)
                ->get()->count() > 0 || Carbon::parse($tournament->date)->toDate() <= Carbon::now()->toDate();
            $is_organisator = $tournament->organisator_id == Auth::id();
        }
        return Inertia::render('Tournament/Details', [
            'tournament' => $tournament,
            'is_enrolled' => $is_enrolled,
            'is_organisator' => $is_organisator
        ]);
    }

    public function edit(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $categories = Category::all();
        return Inertia::render('Tournament/Edit',[
            'categories' => $categories,
            'tournament' => $tournament
        ]);
    }

    public function update(Request  $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $validate = $request->validate(Tournament::$validation_rules);
        if ($tournament->organisator_id != Auth::id()) {
            $request->session()->flash('flash.banner', 'You cannot update someone else\'s event');
            $request->session()->flash('flash.bannerStyle', 'danger');
            return Redirect::route("tournament.show", $id);
        }
        if (Carbon::parse($tournament->date)->toDate() <= Carbon::now()->toDate()) {
            $request->session()->flash('flash.banner', 'You cannot update event that have already started');
            $request->session()->flash('flash.bannerStyle', 'danger');
            return Redirect::route("tournament.show", $id);
        }
        $tournament->name = $validate['name'];
        $tournament->date = $validate['date'];
        $tournament->latitude = $validate['latitude'];
        $tournament->longitude = $validate['longitude'];
        $tournament->place = $validate['place'];
        $tournament->max_participants = $validate['max_participants'];
        $tournament->ranked_players = $validate['ranked_players'];
        $tournament->category_id = $validate['category_id'];
        $tournament->save();
        $request->session()->flash('flash.banner', 'Tournament has been updated');
        $request->session()->flash('flash.bannerStyle', 'success');
        return Redirect::route("tournament.show", $id);


    }


    public  function destroy(Request $request, $id) {
        $tournament = Tournament::findOrFail($id);
        if (Auth::id() == $tournament->organisator_id) {
            $tournament->duels()->delete();
            $tournament->entries()->delete();
            $tournament->logos()->delete();
            $tournament->delete();
            $request->session()->flash('flash.banner', 'Tournament has been deleted');
            $request->session()->flash('flash.bannerStyle', 'success');
            return Redirect::route("tournament.index");
        }
        $request->session()->flash('flash.banner', 'You\'re not the organisator of this event');
        $request->session()->flash('flash.bannerStyle', 'success');
        return Redirect::back();
    }
}
