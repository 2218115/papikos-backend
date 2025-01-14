<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatus;
use Illuminate\Http\Request;
use App\Models\Kos;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function get_list_init() {
        $status = BookingStatus::all();

        return response()->json([
            'message' => 'Berhasil',
            'data' => [
                'status' => $status,
            ],
        ]);
    }

    public function get_all_my_kos_booking(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');

        $booking = Booking::with(['status', 'kos', 'pemesan', 'kos.fotos'])->whereHas('kos', function ($query) use ($user) {
            $query->where('id_pemilik', $user->id);
        })->when($search, function ($q) use ($search) {
            ///   TODO: fix this
            $q->whereHas('kos', function ($q2) use ($search) {
                $q2->where('nama', 'like', '%'.$search.'%');
            });
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

    public function get_booking_detail(Request $request, $id) {
        $booking = Booking::with(['status', 'kos', 'pemesan', 'kos.fotos'])->find($id);

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
            'tanggal_berakhir' => 'required',
            'catatan' => 'required',
            'id_kos' => 'required',
        ]);

        $user = $request->user();

        $kos = Kos::find($validated['id_kos']);
        $nominal = $kos->harga_kos;
        $total = $nominal * $validated['durasi'];

        Booking::create([
            'id_pemesan' => $user->id,
            'id_kos' => $validated['id_kos'],
            'id_status' => 1, // Di ajukan
            'waktu_sewa' => $validated['durasi'],
            'nominal' => $nominal,
            'total' => $total,
            'tanggal_mulai' => Carbon::parse($validated['tanggal_awal']),
            'tanggal_berakhir' => Carbon::parse($validated['tanggal_berakhir']),
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

        return response()->json([
            'mesasge' => 'Berhasil',
            'data' => 'ok',
        ]);
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

        return response()->json([
            'mesasge' => 'Berhasil',
            'data' => 'ok',
        ]);
    }

    public function cancel_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->id_status = 2; // Di Batalkan
        $booking->update();

        return response()->json([
            'mesasge' => 'Berhasil',
            'data' => 'ok',
        ]);
    }
}
