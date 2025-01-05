<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\KosUlasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function get_all_ulasan(Request $request) {}

    public function add_ulasan(Request $request)
    {
        $validated = $request->validate([
            'ulasan' => 'required',
            'rating' => 'required',
            'id_kos' => 'required',
        ]);

        $user = $request->user();
        $kos = Kos::find($validated['id_kos']);

        KosUlasan::create([
            'id_kos' => $kos->id,
            'id_pemberi_ulasan' => $user->id,
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan'],
        ]);

        $final_rating = KosUlasan::where('id_kos', $kos->id)->average('rating');
        $kos->total_rating = $final_rating;
        $kos->save();

        return response()->json([
            'data' => null,
            'message' => 'Berhasil',
        ]);
    }
}
