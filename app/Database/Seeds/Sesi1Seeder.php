<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Throwable;

class Sesi1Seeder extends Seeder
{
    public function run()
    {
        $filePath = ROOTPATH . 'catatan/isipertanyaan1.md';
        if (!file_exists($filePath)) {
            echo "File not found: $filePath\n";
            return;
        }

        $lines = file($filePath);
        $questions = [];
        $currentNo = null;

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines or header separators
            if (empty($line) || strpos($line, '| --') !== false || strpos($line, '| No') !== false) {
                continue;
            }

            // Extract columns: | No | Mirip | Tidak Mirip | Keterangan |
            // Regex handles optional No column (for rows 2, 3, 4 of a question)
            if (preg_match('/^\|\s*(\d*)\s*\|\s*([A-D])\s*\|\s*([A-D])\s*\|\s*(.*?)\s*\|/', $line, $matches)) {
                $no = trim($matches[1]);
                $letter = strtoupper(trim($matches[2]));
                $desc = trim($matches[4]);

                if ($no !== '') {
                    $currentNo = (int)$no;
                }

                if ($currentNo !== null) {
                    $questions[$currentNo][$letter] = $desc;
                }
            }
        }

        if (empty($questions)) {
            echo "No questions parsed from $filePath\n";
            return;
        }

        $db = \Config\Database::connect();
        $table = 'pertanyaan_sesi_1';

        // Clear existing questions first
        try {
            $db->table($table)->emptyTable();
        } catch (Throwable $e) {
            echo "Warning: Could not empty table $table. It might not exist or has constraints.\n";
        }

        $count = 0;
        foreach ($questions as $no => $options) {
            $insertData = [
                'urutan_pertanyaan' => $no,
                'isi_pertanyaan'    => 'Pilih satu yang paling mirip dan satu yang paling tidak mirip dengan diri Anda.',
                'tipe_pertanyaan'   => 'text',
                'pilihan_a'         => $options['A'] ?? '',
                'pilihan_b'         => $options['B'] ?? '',
                'pilihan_c'         => $options['C'] ?? '',
                'pilihan_d'         => $options['D'] ?? '',
                'jawaban_benar'     => 'A', // Default for DISC (not used for scoring normally)
                'status_pertanyaan' => 'Aktif',
                'tanggal_dibuat'    => date('Y-m-d H:i:s'),
                'tanggal_diubah'    => date('Y-m-d H:i:s'),
            ];

            try {
                $db->table($table)->insert($insertData);
                $count++;
            } catch (Throwable $e) {
                echo "Error inserting question $no: " . $e->getMessage() . "\n";
            }
        }

        echo "Successfully imported $count questions to $table.\n";
    }
}
