<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipeKos;
use App\Models\KosStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TipeKos::insert([
            ['nama' => 'Laki-Laki'],
            ['nama' => 'Perempuan'],
            ['nama' => 'Campur'],
        ]);

        KosStatus::insert([
            ['nama' => 'Diajukan'],
            ['nama' => 'Disetujui'],
            ['nama' => 'Ditolak'],
            ['nama' => 'Ditahan'],
        ]);
    }
}
