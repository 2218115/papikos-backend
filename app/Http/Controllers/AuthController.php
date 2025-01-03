<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PemilikKos;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function user_login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);


        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages(['Tidak di temukan pengguna dengan kredential yang di berikan.']);
        }

        $is_password_sama = Hash::check($validated['password'], $user->password);
        if (!$is_password_sama) {
            throw ValidationException::withMessages(['Tidak di temukan pengguna dengan kredential yang di berikan.']);
        }

        return response()->json([
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
                'name' => $user->name,
            ],
            'token' => $user->createToken('user_token')->plainTextToken,
        ]);
    }

    public function user_register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|min:8',
            'email' => 'required|email|unique:users|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);


        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'USER',
        ]);

        return response()->json([
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
                'name' => $user->name,
            ],
            'token' => $user->createToken('user_token')->plainTextToken,
        ]);
    }

    public function pemilik_kos_register(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'required|image',
            'nama' => 'required|min:8',
            'email' => 'required|email|unique:users|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'foto_identitas' => 'required|image',
            'no_wa' => 'required',
            'alamat' => 'required',
        ]);

        // simpan avatar
        $avatar_filename = Str::random(40) . '.' . $request->file('foto_identitas')->getClientOriginalExtension();
        $avatar_path = $request->file('foto_identitas')->storeAs('foto_identitas', $avatar_filename, 'local');

        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'PEMILIK_KOS',
            'avatar' => $avatar_path,
        ]);

        // simpan foto identitas
        $foto_identitas_filename = $user->id . 'xx' . Str::random(40) . '.' . $request->file('foto_identitas')->getClientOriginalExtension();
        $foto_identitas_path = $request->file('foto_identitas')->storeAs('foto_identitas', $foto_identitas_filename, 'local');

        // tambahan data untuk pemilik_kos
        $pemilik_kos_information = PemilikKos::create([
            'id_user' => $user->id,
            'foto_identitas' => $foto_identitas_path,
            'no_wa' => $validated['no_wa'],
            'alamat' => $validated['alamat'],
        ]);

        return response()->json([
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
                'name' => $user->name,
            ],
            'token' => $user->createToken('user_token')->plainTextToken,
        ]);
    }

    public function current_user(Request $request)
    {
        $user = $request->user();

        $informasi_detail = null;
        if ($user->role == 'PEMILIK_KOS') {
            $informasi_detail = PemilikKos::where('id_user', $user->id)->first();
        }

        return response()->json([
            'message' => 'Berhasil',
            'data' => [
                'user' => $user,
                'detail' => $informasi_detail,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
