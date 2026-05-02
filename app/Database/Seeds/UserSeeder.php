<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user'        => 'HRD001',
                'nama_lengkap'   => 'Rina HRD',
                'jenis_kelamin'  => 'Perempuan',
                'username'       => 'hrd001',
                'password'       => password_hash('hrd123', PASSWORD_DEFAULT),
                'role'           => 'hrd',
                'posisi_dilamar' => 'Human Resource',
                'alamat'         => 'Kantor Pusat',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id_user'        => 'HRD002',
                'nama_lengkap'   => 'Bima HRD',
                'jenis_kelamin'  => 'Laki-laki',
                'username'       => 'hrd002',
                'password'       => password_hash('hrd123', PASSWORD_DEFAULT),
                'role'           => 'hrd',
                'posisi_dilamar' => 'Human Resource',
                'alamat'         => 'Kantor Cabang',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id_user'        => 'PGW001',
                'nama_lengkap'   => 'Andi Saputra',
                'jenis_kelamin'  => 'Laki-laki',
                'username'       => 'pgw001',
                'password'       => password_hash('pegawai123', PASSWORD_DEFAULT),
                'role'           => 'user',
                'posisi_dilamar' => 'Staff Administrasi',
                'alamat'         => 'Jl. Merdeka No. 10',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id_user'        => 'PGW002',
                'nama_lengkap'   => 'Maya Lestari',
                'jenis_kelamin'  => 'Perempuan',
                'username'       => 'pgw002',
                'password'       => password_hash('pegawai123', PASSWORD_DEFAULT),
                'role'           => 'user',
                'posisi_dilamar' => 'Customer Support',
                'alamat'         => 'Jl. Mawar No. 5',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pegawai')->insertBatch($data);
    }
}
