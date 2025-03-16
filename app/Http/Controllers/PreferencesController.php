<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preference;
use Illuminate\Support\Facades\Auth;

class PreferencesController extends Controller
{
    public function savePreferences(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array'
        ]);

        $preferences = Preference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'sources' => $request->sources,
                'categories' => $request->categories,
                'authors' => $request->authors
            ]
        );

        return response()->json(['message' => 'Preferences saved', 'data' => $preferences]);
    }

    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = Preference::where('user_id', $user->id)->first();

        return response()->json($preferences);
    }
}
