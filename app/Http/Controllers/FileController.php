<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function get_private_file(Request $request, $path, $filename) {
        $path = $path . '/' . $filename;

        // jika user bukan admin jangan perbolehkan akses private file
        $is_admin = $request->user()->role == 'ADMIN';

        // jika user bukan pemilik dari file jangan perbolehkan akses file
        $user_id = (int)explode('xx', $filename)[0];
        if ($request->user()->id != $user_id && !$is_admin) {
            return response()->json(['message' => 'Tidak mendapatkan akses'], 401);
        }


        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'File tidak di temukan'], 404);
        }

        return Storage::disk('local')->download($path);
    }
}
