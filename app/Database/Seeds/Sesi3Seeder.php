<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Throwable;

class Sesi3Seeder extends Seeder
{
    public function run()
    {
        $jsonPath = ROOTPATH . 'catatan/sesi3.md';
        if (!file_exists($jsonPath)) {
            echo "File not found: $jsonPath\n";
            return;
        }

        $jsonData = file_get_contents($jsonPath);
        $data = json_decode($jsonData, true);

        if (!$data || !isset($data['questions'])) {
            echo "Invalid JSON data in $jsonPath\n";
            return;
        }

        $table = 'pertanyaan_sesi_3';

        // Clear existing questions first if needed, but maybe just append or use a flag.
        // For now, let's empty it as it's an import.
        $this->db->table($table)->emptyTable();

        foreach ($data['questions'] as $q) {
            $insertData = [
                'urutan_pertanyaan' => $q['number'],
                'tipe_pertanyaan'   => 'text',
                'isi_pertanyaan'    => $q['question'],
                'pilihan_a'         => $q['options']['A'] ?? null,
                'pilihan_b'         => $q['options']['B'] ?? null,
                'pilihan_c'         => $q['options']['C'] ?? null,
                'pilihan_d'         => $q['options']['D'] ?? null,
                'pilihan_e'         => $q['options']['E'] ?? null,
                'tipe_pilihan_a'    => 'text',
                'tipe_pilihan_b'    => 'text',
                'tipe_pilihan_c'    => 'text',
                'tipe_pilihan_d'    => 'text',
                'tipe_pilihan_e'    => 'text',
                'jawaban_benar'     => 'A', // Defaulting to A as there's no "correct" answer for VAK
                'status_pertanyaan' => 'Aktif',
                'tanggal_dibuat'    => date('Y-m-d H:i:s'),
            ];

            try {
                $this->db->table($table)->insert($insertData);
            } catch (Throwable $e) {
                echo "Error inserting question {$q['number']}: " . $e->getMessage() . "\n";
            }
        }

        echo "Successfully imported " . count($data['questions']) . " questions to $table.\n";
    }
}
