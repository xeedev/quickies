<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle a tool in the authenticated user's favourites.
     */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'href' => ['required', 'string', 'max:255'],
        ]);

        $favorited = $request->user()->toggleFavorite($data['href']);

        return response()->json([
            'favorited' => $favorited,
            'favorites' => $request->user()->favoriteTools(),
        ]);
    }
}
