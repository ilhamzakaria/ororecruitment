<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionAnswerModel extends Model
{
    protected $primaryKey       = 'id_jawaban';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_pegawai',
        'nama_pegawai',
        'id_pertanyaan',
        'nomor_pertanyaan',
        'jawaban_pegawai',
        'jawaban_benar',
        'status_jawaban',
        'nilai',
        'tanggal_menjawab',
    ];

    public function setSession(int $session)
    {
        $this->table = "jawaban_sesi_$session";
        return $this;
    }

    public function getByParticipant(string $idPegawai)
    {
        return $this->where('id_pegawai', $idPegawai)
                    ->orderBy('nomor_pertanyaan', 'ASC')
                    ->findAll();
    }

    public function getScoreSummary(string $idPegawai)
    {
        $answers = $this->where('id_pegawai', $idPegawai)->findAll();
        $total = count($answers);
        $correct = 0;
        $totalNilai = 0;

        foreach ($answers as $a) {
            if ($a['status_jawaban'] === 'Benar') {
                $correct++;
            }
            $totalNilai += (int)$a['nilai'];
        }

        return [
            'total' => $total,
            'correct' => $correct,
            'score' => $totalNilai,
        ];
    }
}
