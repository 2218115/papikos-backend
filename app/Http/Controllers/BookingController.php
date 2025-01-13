<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function get_all_my_kos_booking(Request $request)
    {
        $user = $request->user();

        $booking = Booking::with(['status', 'kos', 'pemesan', 'kos.fotos'])->whereHas('kos', function ($query) use ($user) {
            $query->where('id_pemilik', $user->id);
        })->paginate(10);

        return response()->json([
            'message' => 'Berhasil',
            'data' => $booking,
        ]);
    }

    public function get_all_my_booking(Request $request) {
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

        $kos = Kos::find($validated['id_kos']);
        $nominal = $kos->harga_sewa;
        $total = $nominal * $validated['durasi'];

        Booking::create([
            'id_pemesan' => $user->id,
            'id_kos' => $validated['id_kos'],
            'status' => 1, // Di ajukan
            'waktu_sewa' => $validated['durasi'],
            'nominal' => $nominal,
            'total' => $total,
            'tanggal_mulai' => $validated['tanggal_awal'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ]);

        return response()->json([
            'mesasge' => 'Berhasil',
            'data' => 'ok',
        ]);
    }

    public function approve_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->id_status = 3; // Di Setuji
        $booking->update();
    }

    public function reject_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->id_status = 5; // Di Tolak
        $booking->update();
    }

    public function done_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->id_status = 4; // Selesai
        $booking->update();
    }

    public function cancel_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->id_status = 2; // Di Batalkan
        $booking->update();
    }
}
