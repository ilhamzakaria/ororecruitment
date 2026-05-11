<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResetUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Find user ID by username 'contoh2'
        $user = $db->table('users')->where('username', 'contoh2')->get()->getRowArray();
        if (!$user) {
            echo "User 'contoh2' not found.\n";
            return;
        }
        
        $idPegawai = $user['id'];
        echo "Resetting status for user: contoh2 (ID: $idPegawai)\n";


        // 1. Reset Peserta Table
        $db->table('peserta')->where('id_user', $idPegawai)->update([
            'status'           => 'draft',
            'is_blocked'       => 0,
            'violations_count' => 0,
            'tab_switches'     => 0,
            'current_session'  => 1,
            'current_question' => 0,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // 2. Reset status_sesi_peserta
        $db->table('status_sesi_peserta')->where('id_pegawai', $idPegawai)->update([
            'status_sesi' => 'belum_mulai',
            'waktu_mulai' => null,
            'waktu_sisa'  => 600, 
            'tanggal_selesai' => null,
        ]);

        // 3. Reset kontrol_sesi_pegawai
        // Set Session 1 to 'dibuka' so user can start, but 2 and 3 to 'belum_dibuka'
        $db->table('kontrol_sesi_pegawai')->where('id_pegawai', $idPegawai)->where('nomor_sesi', 1)->update(['status_sesi' => 'dibuka']);
        $db->table('kontrol_sesi_pegawai')->where('id_pegawai', $idPegawai)->where('nomor_sesi', 2)->update(['status_sesi' => 'belum_dibuka']);
        $db->table('kontrol_sesi_pegawai')->where('id_pegawai', $idPegawai)->where('nomor_sesi', 3)->update(['status_sesi' => 'belum_dibuka']);

        // Ensure records exist for 2 and 3 if they don't
        foreach ([2, 3] as $s) {
            $exists = $db->table('kontrol_sesi_pegawai')->where('id_pegawai', $idPegawai)->where('nomor_sesi', $s)->get()->getRowArray();
            if (!$exists) {
                $db->table('kontrol_sesi_pegawai')->insert([
                    'id_pegawai' => $idPegawai,
                    'nama_pegawai' => 'contoh2',
                    'nomor_sesi' => $s,
                    'status_sesi' => 'belum_dibuka',
                    'tanggal_dibuka' => date('Y-m-d'),
                    'waktu_dibuka' => date('H:i:s'),
                    'dibuka_oleh' => 'System (Reset)'
                ]);
            }
        }


        echo "Successfully reset status for user: $idPegawai\n";
    }
}
