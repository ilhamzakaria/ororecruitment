<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Throwable;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'isi_pertanyaan'   => 'Mana dari ke-5 ini yang paling TIDAK mirip dengan 4 yang lain?',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'BERUANG',
                'pilihan_b'        => 'ULAR',
                'pilihan_c'        => 'SAPI',
                'pilihan_d'        => 'ANJING',
                'pilihan_e'        => 'HARIMAU',
                'jawaban_benar'    => 'B',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Jika Anda mengatur ulang kata “LINKECI”, Anda akan mendapat nama sebuah:',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'LAUTAN',
                'pilihan_b'        => 'NEGARA',
                'pilihan_c'        => 'PROPINSI',
                'pilihan_d'        => 'KOTA',
                'pilihan_e'        => 'HEWAN',
                'jawaban_benar'    => 'E',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Pilihlah gambar yang merupakan kelanjutan logis dari pola di atas.',
                'tipe_pertanyaan'  => 'gambar',
                'gambar_pertanyaan'=> 'assets/aptitude/questions/q03.png',
                'pilihan_a'        => 'assets/aptitude/questions/q03_a.png',
                'pilihan_b'        => 'assets/aptitude/questions/q03_b.png',
                'pilihan_c'        => 'assets/aptitude/questions/q03_c.png',
                'pilihan_d'        => 'assets/aptitude/questions/q03_d.png',
                'pilihan_e'        => 'assets/aptitude/questions/q03_e.png',
                'jawaban_benar'    => 'B',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Mana dari ke-5 ini yang paling TIDAK mirip dengan 4 yang lain?',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'KENTANG',
                'pilihan_b'        => 'JAGUNG',
                'pilihan_c'        => 'APEL',
                'pilihan_d'        => 'WORTEL',
                'pilihan_e'        => 'KACANG',
                'jawaban_benar'    => 'C',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Manakah gambar yang sesuai untuk melengkapi pola berikut?',
                'tipe_pertanyaan'  => 'gambar',
                'gambar_pertanyaan'=> 'assets/aptitude/questions/q05.png',
                'pilihan_a'        => 'assets/aptitude/questions/q05_a.png',
                'pilihan_b'        => 'assets/aptitude/questions/q05_b.png',
                'pilihan_c'        => 'assets/aptitude/questions/q05_c.jpg',
                'pilihan_d'        => 'assets/aptitude/questions/q05_d.jpg',
                'pilihan_e'        => 'assets/aptitude/questions/q05_e.jpg',
                'jawaban_benar'    => 'B',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Saat ini John berumur 12 tahun, yaitu 3 kali lebih tua dari adiknya. Berapa umur John saat umurnya 2 kali lebih tua dari umur adiknya?',
                'tipe_pertanyaan'  => 'angka',
                'pilihan_a'        => '15',
                'pilihan_b'        => '16',
                'pilihan_c'        => '18',
                'pilihan_d'        => '20',
                'pilihan_e'        => '21',
                'jawaban_benar'    => 'B',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Jika “Kakak Laki-Laki” itu “Kakak Perempuan”, maka “Keponakan Perempuan” adalah:',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'IBU',
                'pilihan_b'        => 'ANAK PEREMPUAN',
                'pilihan_c'        => 'BIBI',
                'pilihan_d'        => 'PAMAN',
                'pilihan_e'        => 'KEPONAKAN LAKI-LAKI',
                'jawaban_benar'    => 'E',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Apa sinonim dari kata "Sombong"?',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'Baik hati',
                'pilihan_b'        => 'Rendah hati',
                'pilihan_c'        => 'Angkuh',
                'pilihan_d'        => 'Malu-malu',
                'pilihan_e'        => 'Sederhana',
                'jawaban_benar'    => 'C',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Jika “Susu” itu “Gelas”, maka “Surat” itu:',
                'tipe_pertanyaan'  => 'text',
                'pilihan_a'        => 'STEMPEL',
                'pilihan_b'        => 'BALLPOIN',
                'pilihan_c'        => 'AMPLOP',
                'pilihan_d'        => 'BUKU',
                'pilihan_e'        => 'KIRIMAN',
                'jawaban_benar'    => 'C',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
            [
                'isi_pertanyaan'   => 'Perhatikan gambar berikut dan tentukan pilihan yang tepat untuk melengkapi deret tersebut.',
                'tipe_pertanyaan'  => 'gambar',
                'gambar_pertanyaan'=> 'assets/aptitude/questions/q10.png',
                'pilihan_a'        => 'assets/aptitude/questions/q10_a.jpg',
                'pilihan_b'        => 'assets/aptitude/questions/q10_b.jpg',
                'pilihan_c'        => 'assets/aptitude/questions/q10_c.jpg',
                'pilihan_d'        => 'assets/aptitude/questions/q10_d.jpg',
                'pilihan_e'        => 'assets/aptitude/questions/q10_e.jpg',
                'jawaban_benar'    => 'A',
                'status_pertanyaan'=> 'Aktif',
                'dibuat_oleh'      => 'SYSTEM',
                'role_pembuat'     => 'admin',
                'tanggal_dibuat'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Clear existing questions first
        $this->db->table('pertanyaan_tes')->emptyTable();

        foreach ($data as $q) {
            try {
                $this->db->table('pertanyaan_tes')->insert($q);
            } catch (Throwable $e) {
                echo "Error inserting question: " . $e->getMessage() . "\n";
            }
        }
    }
}
