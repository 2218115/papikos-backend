<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kos;
use App\Models\KosFasilitas;
use App\Models\KosPeraturan;
use App\Models\KosFotos;
use App\Models\KosStatusHistory;
use App\Models\KosStatus;
use App\Models\TipeKos;
use App\Models\User;

use Exception;

class KosController extends Controller
{
    public function create_kos(Request $request)
    {
        $user = $request->user();

        $validated = array();

        if ($user->role === 'ADMIN') {
            $validated = $request->validate([
                'nama' => 'required',
                'harga_kos' => 'required',
                'minimal_sewa' => 'required',
                'lokasi_kos' => 'required',
                'kamar_tersedia' => 'required',
                'narahubung_kos' => 'required',
                'tipe_kos' => 'required|exists:tipe_kos,id',
                'embed_gmaps' => 'required',
                'kos_fotos' => 'required|array|min:1',
                'kos_fasilitas' => 'required|array|min:1',
                'kos_peraturan' => 'required|array|min:1',
                'pemilik' => 'required',
            ]);
        } else {
            $validated = $request->validate([
                'nama' => 'required',
                'harga_kos' => 'required',
                'minimal_sewa' => 'required',
                'lokasi_kos' => 'required',
                'kamar_tersedia' => 'required',
                'narahubung_kos' => 'required',
                'tipe_kos' => 'required|exists:tipe_kos,id',
                'embed_gmaps' => 'required',
                'kos_fotos' => 'required|array|min:1',
                'kos_fasilitas' => 'required|array|min:1',
                'kos_peraturan' => 'required|array|min:1',
            ]);
            $validated['pemilik'] = $user->id;
        }


        try {

            $kos = Kos::create([
                'nama' => $validated['nama'],
                'harga_kos' => $validated['harga_kos'],
                'minimal_sewa' => $validated['minimal_sewa'],
                'lokasi_kos' => $validated['lokasi_kos'],
                'kamar_tersedia' => $validated['kamar_tersedia'],
                'narahubung_kos' => $validated['narahubung_kos'],
                'id_tipe_kos' => $validated['tipe_kos'],
                'embed_gmaps' => $validated['embed_gmaps'],
                'total_rating' => 0,
                'id_pemilik' => $validated['pemilik'],
            ]);

            $fasilitas_kos = [];
            foreach ($validated['kos_fasilitas'] as $item) {
                $fasilitas_kos[] = [
                    'id_kos' => $kos->id,
                    'nama' => $item,
                ];
            }
            KosFasilitas::insert($fasilitas_kos);

            $peraturan_kos = [];
            foreach ($validated['kos_fasilitas'] as $item) {
                $peraturan_kos[] = [
                    'id_kos' => $kos->id,
                    'nama' => $item,
                ];
            }
            KosPeraturan::insert($peraturan_kos);

            $kos_fotos = [];
            foreach ($request->file('kos_fotos') as $file) {
                $filename = uniqid() . '-' . $file->getClientOriginalName();
                $file->storeAs('foto_kos', $filename, 'public');
                $kos_fotos[] = [
                    'id_kos' => $kos->id,
                    'path' => $filename,
                ];
            }
            KosFotos::insert($kos_fotos);

            // history status kos
            $catatan_template = '#' . $user->id;

            if ($user->role == 'ADMIN') {
                $catatan_template = $catatan_template . '[Admin]' . '[' . $user->name . ']' . ' Melakukan Penambahan data kos.';
            } else if ($user->role == 'PEMILIK_KOS') {
                $catatan_template = $catatan_template . '[Pemilik Kos]' . '[' . $user->name . ']' . ' Melakukan Pengajuan data kos.';
            }
            KosStatusHistory::create([
                'id_kos' => $kos->id,
                'id_status' => '1', // 1 Diajukan
                'id_pembuat' => $user->id,
                'catatan' => $catatan_template,
            ]);

            return response()->json([
                'message' => 'Berhasil',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_all_kos(Request $request)
    {
        $search = $request->query('search');
        $statusId = $request->query('status_id');
        $pemilikId = $request->query('pemilik');

        $status_filter = KosStatus::all();

        $kos = Kos::with('fotos')->when($search, function ($query, $search) {
            return $query->where('nama', 'LIKE', "%{$search}%");
        })
            ->when($statusId, function ($query, $statusId) {
                return $query->whereHas('current_status', function ($subQuery) use ($statusId) {
                    $subQuery->where('id_status', $statusId);
                });
            })
            ->when($pemilikId, function ($query, $pemilikId) {
                return $query->where('id_pemilik', $pemilikId);
            })
            ->with(['tipe_kos', 'pemilik', 'current_status.status'])
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $kos,
            'status_filter' => $status_filter,
        ]);
    }

    public function get_detail_kos_data($id)
    {
        $kos = Kos::with([
            'tipe_kos',
            'fotos',
            'fasilitas_kos',
            'peraturan_kos',
            'history_status',
            'ulasan' => function ($query) {
                $query->whereNull('id_balasan');
            },
            'pemilik',
            'ulasan.pemberi_ulasan',
            'ulasan.balasan',
            'ulasan.balasan.pemberi_ulasan',
            'current_status.status',
        ])->find($id);

        return response()->json([
            'message' => 'Berhasil',
            'data' => $kos,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = $request->user();
        $kos = Kos::find($id);

        if ($kos->current_status->id_status == 2) {
            return response()->json([
                'message' => 'Berhasil',
            ]);
        }

        $catatan_template = '#' . $user->id;
        $catatan_template = $catatan_template . '[Admin]' . '[' . $user->name . ']' . ' Melakukan Approve data kos.';

        KosStatusHistory::create([
            'id_kos' => $kos->id,
            'id_status' => '2', // 2 Approve
            'id_pembuat' => $user->id,
            'catatan' => $catatan_template,
        ]);

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan' => 'required',
        ]);

        $user = $request->user();
        $kos = Kos::with('history_status')->find($id);

        if ($kos->current_status->id_status == 3) {
            return response()->json([
                'message' => 'Berhasil',
            ]);
        }

        $catatan_template = '#' . $user->id;
        $catatan_template = $catatan_template . '[Admin]' . '[' . $user->name . ']' . ' Melakukan Reject data kos.';
        $catatan_template = $catatan_template . '<br>' . $validated['catatan'];

        KosStatusHistory::create([
            'id_kos' => $kos->id,
            'id_status' => '3', // 3 Ditolak
            'id_pembuat' => $user->id,
            'catatan' => $catatan_template,
        ]);

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }

    public function suspend(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan' => 'required',
        ]);

        $user = $request->user();
        $kos = Kos::with('history_status')->find($id);

        if ($kos->current_status->id_status == 3) {
            return response()->json([
                'message' => 'Berhasil',
            ]);
        }

        $catatan_template = '#' . $user->id;
        $catatan_template = $catatan_template . '[Admin]' . '[' . $user->name . ']' . ' Me Nangguhkan data kos.';
        $catatan_template = $catatan_template . '<br>' . $validated['catatan'];

        KosStatusHistory::create([
            'id_kos' => $kos->id,
            'id_status' => '4', // 3 Di tangguhkan
            'id_pembuat' => $user->id,
            'catatan' => $catatan_template,
        ]);

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }

    public function update_kos()
    {
        return response()->json([
            'data' => 'TODO',
        ]);
    }

    public function get_analitik_data(Request $request)
    {

        $count_all_kos = Kos::count();
        $count_approved_kos = Kos::whereHas('history_status', function ($query) {
            $query->where('id_status', 2)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();
        $count_rejected_kos = Kos::whereHas('history_status', function ($query) {
            $query->where('id_status', 3)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();
        $count_waiting_kos = Kos::whereHas('history_status', function ($query) {
            $query->where('id_status', 1)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();

        return response()->json([
            'data' => [
                'count_all_kos' => $count_all_kos,
                'count_approved_kos' => $count_approved_kos,
                'count_waiting_kos' => $count_waiting_kos,
                'count_rejected_kos' => $count_rejected_kos,
            ],
        ]);
    }

    public function get_form_init()
    {
        $tipe_kos = TipeKos::all();
        $pemilik_kos = User::where('role', 'PEMILIK_KOS')->get();

        return response()->json([
            'message' => 'Berhasil',
            'data' => [
                'tipe_kos' => $tipe_kos,
                'pemilik_kos' => $pemilik_kos,
            ],
        ]);
    }

    public function get_analitik_data_pemilik(Request $request)
    {
        $user = $request->user();

        $count_all_kos = Kos::where('id_pemilik', $user->id)->count();
        $count_approved_kos = Kos::whereHas('history_status', function ($query) use ($user) {
            $query->where('id_status', 2)->where('id_pemilik', $user->id)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();

        $count_rejected_kos = Kos::whereHas('history_status', function ($query) use ($user) {
            $query->where('id_status', 3)->where('id_pemilik', $user->id)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();

        $count_waiting_kos = Kos::whereHas('history_status', function ($query) use ($user) {
            $query->where('id_status', 1)->where('id_pemilik', $user->id)
                ->whereRaw('created_at = (SELECT MAX(created_at) FROM kos_status_history WHERE kos_status_history.id_kos = kos.id)');
        })->count();

        return response()->json([
            'data' => [
                'count_all_kos' => $count_all_kos,
                'count_approved_kos' => $count_approved_kos,
                'count_waiting_kos' => $count_waiting_kos,
                'count_rejected_kos' => $count_rejected_kos,
            ],
        ]);
    }
}
