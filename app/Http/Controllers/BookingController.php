<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function get_all_my_booking(Request $request)
    {
        $user = $request->user();

        $booking = Booking::with(['status', 'kos', 'pemesan', 'kos.fotos'])->where('id_pemesan', $user->id)->paginate(10);

        return response()->json([
            'message' => 'Berhasil',
            'data' => $booking,
        ]);
    }

    public function add_booking(Request $request)
    {
        $validated = $request->validate([
            'tanggal_awal' => 'required',
            'durasi' => 'required',
            'catatan' => 'required',
        ]);

        $user = $request->user();

        return response()->json([
            'mesasge' => 'Berhasil',
            'data' => 'ok',
        ]);
    }
}
