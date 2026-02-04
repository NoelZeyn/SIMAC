<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua penempatan (25 data)
        $penempatans = DB::table('penempatan')
            ->select('id', 'id_bidang_fk')
            ->orderBy('id') // penting: urut & stabil
            ->get();

        if ($penempatans->isEmpty()) {
            $this->command->warn('Seeder Admin gagal: Tabel Penempatan kosong.');
            return;
        }

        $tingkatanList = [
            'user',
            'user_review',
            'admin',
            'anggaran',
            'asman',
            'manajer'
        ];

        $accessList = ['active', 'inactive'];

        /**
         * =================================
         * Admin random (ROTASI penempatan)
         * =================================
         */
        $penempatanIndex = 0;
        $totalPenempatan = $penempatans->count();

        for ($i = 0; $i < 20; $i++) {
            $penempatan = $penempatans[$penempatanIndex];

            Admin::create([
                'NID'                => $faker->unique()->numerify('NID#######'),
                'password'           => Hash::make('password123'),
                'id_penempatan_fk'   => $penempatan->id,
                'id_bidang_fk'       => $penempatan->id_bidang_fk,
                'tingkatan_otoritas' => $faker->randomElement($tingkatanList),
                'access'             => $faker->randomElement($accessList),
                'password_changed_at' => now(),
            ]);

            // rotasi 1–25, bukan lompat random
            $penempatanIndex = ($penempatanIndex + 1) % $totalPenempatan;
        }

        /**
         * =================================
         * Superadmin utama (penempatan pertama)
         * =================================
         */
        $penempatanUtama = $penempatans->first();



        /**
         * =================================
         * Superadmin IT (EQA)
         * =================================
         */
        $penempatanIT = DB::table('penempatan')
            ->where('nama_penempatan', 'IT')
            ->first();

        if ($penempatanIT) {
            Admin::create([
                'NID'                => '8813066ZJA',
                'password'           => Hash::make('password123'),
                'id_penempatan_fk'   => $penempatanIT->id,
                'id_bidang_fk'       => $penempatanIT->id_bidang_fk,
                'tingkatan_otoritas' => 'superadmin',
                'access'             => 'active',
                'password_changed_at' => now(),
            ]);
        }

        /**
         * =================================
         * User Keuangan (Business Support)
         * =================================
         */
        $penempatanKeuangan = DB::table('penempatan')
            ->where('nama_penempatan', 'KEUANGAN')
            ->first();

        if ($penempatanKeuangan) {
            Admin::create([
                'NID'                => '8813066ZJB',
                'password'           => Hash::make('password123'),
                'id_penempatan_fk'   => $penempatanKeuangan->id,
                'id_bidang_fk'       => $penempatanKeuangan->id_bidang_fk,
                'tingkatan_otoritas' => 'asman',
                'access'             => 'active',
                'password_changed_at' => now(),
            ]);
            Admin::create([
                'NID'                => '8813066ZJY',
                'password'           => Hash::make('password123'),
                'id_penempatan_fk'   => $penempatanKeuangan->id,
                'id_bidang_fk'       => $penempatanKeuangan->id_bidang_fk,
                'tingkatan_otoritas' => 'manajer',
                'access'             => 'active',
                'password_changed_at' => now(),
            ]);
        }
    }
}
