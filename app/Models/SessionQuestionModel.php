<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionQuestionModel extends Model
{
    protected $primaryKey       = 'id_pertanyaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'urutan_pertanyaan',
        'tipe_pertanyaan',
        'isi_pertanyaan',
        'gambar_pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'gambar_pilihan_a',
        'gambar_pilihan_b',
        'gambar_pilihan_c',
        'gambar_pilihan_d',
        'gambar_pilihan_e',
        'tipe_pilihan_a',
        'tipe_pilihan_b',
        'tipe_pilihan_c',
        'tipe_pilihan_d',
        'tipe_pilihan_e',
        'jawaban_benar',
        'status_pertanyaan',
        'tanggal_dibuat',
        'tanggal_diubah',
    ];

    public function setSession(int $session)
    {
        $this->table = "pertanyaan_sesi_$session";
        return $this;
    }

    public function autoInitializeOrder()
    {
        $nullQuestions = $this->where('urutan_pertanyaan', null)
            ->orWhere('urutan_pertanyaan', 0)
            ->orderBy('id_pertanyaan', 'ASC')
            ->findAll();

        if (!empty($nullQuestions)) {
            $maxOrder = $this->selectMax('urutan_pertanyaan')->first();
            $nextOrder = ($maxOrder['urutan_pertanyaan'] ?? 0) + 1;
            foreach ($nullQuestions as $nq) {
                $this->update($nq['id_pertanyaan'], ['urutan_pertanyaan' => $nextOrder++]);
            }
        }
    }

    public function getNextOrder()
    {
        $maxOrder = $this->selectMax('urutan_pertanyaan')->first();
        return ($maxOrder['urutan_pertanyaan'] ?? 0) + 1;
    }

    public function deleteAndShift(int $id)
    {
        $question = $this->find($id);
        if ($question) {
            $order = $question['urutan_pertanyaan'];
            $this->delete($id);
            
            // Shift subsequent questions down to fill the gap
            $this->where('urutan_pertanyaan >', $order)
                 ->set('urutan_pertanyaan', 'urutan_pertanyaan - 1', false)
                 ->update();
        }
    }

    public function reorder(int $id, $direction)
    {
        $currentQuestion = $this->find($id);
        if (!$currentQuestion) {
            return false;
        }

        $currentOrder = (int)$currentQuestion['urutan_pertanyaan'];
        $total = $this->countAllResults();

        if ($direction === 'up' && $currentOrder > 1) {
            $targetOrder = $currentOrder - 1;
        } elseif ($direction === 'down' && $currentOrder < $total) {
            $targetOrder = $currentOrder + 1;
        } elseif (is_numeric($direction)) {
            $targetOrder = (int)$direction;
        } else {
            return false;
        }

        if ($targetOrder < 1) $targetOrder = 1;
        if ($targetOrder > $total) $targetOrder = $total;

        if ($targetOrder === $currentOrder) {
            return true;
        }

        if ($targetOrder < $currentOrder) {
            // Moving up: shift others down
            $this->where('urutan_pertanyaan >=', $targetOrder)
                 ->where('urutan_pertanyaan <', $currentOrder)
                 ->set('urutan_pertanyaan', 'urutan_pertanyaan + 1', false)
                 ->update();
        } else {
            // Moving down: shift others up
            $this->where('urutan_pertanyaan >', $currentOrder)
                 ->where('urutan_pertanyaan <=', $targetOrder)
                 ->set('urutan_pertanyaan', 'urutan_pertanyaan - 1', false)
                 ->update();
        }

        $this->update($id, ['urutan_pertanyaan' => $targetOrder]);
        return true;
    }
}
