<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentLogo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TournamentLogoController extends Controller
{
    public function store(Request $request, $id): RedirectResponse
    {
        $tournament = Tournament::findOrFail($id);
        $request->validate(TournamentLogo::$validation_rules);

        if (Auth::id() == $tournament->organisator_id) {
            $logo = new TournamentLogo();

            if ($request->file()) {
                $fileName = time().'_'.$request->file('file')->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

                $logo->name = $fileName;
                $logo->path = '/storage/'.$filePath;
                $logo->tournament_id = $tournament->id;
                $logo->save();
                $tournament->logos()->save($logo);
                $request->session()->flash('flash.banner', 'Logo has been successfully uploaded');
                $request->session()->flash('flash.bannerStyle', 'success');
                return back();
            }
            $request->session()->flash('flash.banner', 'Cannot upload file, try again later');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return back();
        }
        else {
            $request->session()->flash('flash.banner', 'You\'re not the event organisator');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return back();

        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate(['id' => 'required|exists:tournament_logos,id']);
        $logo = TournamentLogo::findOrFail($validated['id']);
        if ($logo->tournament()->organisator()->id == Auth::id()) {
            Storage::delete($logo->path);
            $logo->delete();
            $request->session()->flash('flash.banner', 'Logo has been deleted');
            $request->session()->flash('flash.bannerStyle', 'success');
            return back();
        }
        $request->session()->flash('flash.banner', 'Cannot delete logo, try again later');
        $request->session()->flash('flash.bannerStyle', 'danger');
        return back();
    }
}
