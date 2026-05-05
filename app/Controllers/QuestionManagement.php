<?php

namespace App\Controllers;

use App\Models\SessionQuestionModel;
use App\Models\SessionAnswerModel;
use Throwable;

class QuestionManagement extends BaseController
{
    protected $questionModel;
    protected $answerModel;

    public function __construct()
    {
        $this->questionModel = new SessionQuestionModel();
        $this->answerModel = new SessionAnswerModel();
    }

    public function index($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $this->questionModel->setSession($session);

        // Auto-initialize null urutan_pertanyaan
        $nullQuestions = $this->questionModel->where('urutan_pertanyaan', null)->orWhere('urutan_pertanyaan', 0)->orderBy('id_pertanyaan', 'ASC')->findAll();
        if (!empty($nullQuestions)) {
            $maxOrder = $this->questionModel->selectMax('urutan_pertanyaan')->first();
            $nextOrder = ($maxOrder['urutan_pertanyaan'] ?? 0) + 1;
            foreach ($nullQuestions as $nq) {
                $this->questionModel->update($nq['id_pertanyaan'], ['urutan_pertanyaan' => $nextOrder++]);
            }
        }

        return view('hrd_manage_questions', [
            'questions' => $this->questionModel->orderBy('urutan_pertanyaan', 'ASC')->findAll(),
            'authUser'  => $this->authUser(),
            'logoutUrl' => site_url('logout'),
            'currentSession' => $session,
        ]);
    }

    public function add($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $this->questionModel->setSession($session);
        $authUser = $this->authUser();
        
        // Get max order
        $maxOrder = $this->questionModel->selectMax('urutan_pertanyaan')->first();
        $nextOrder = ($maxOrder['urutan_pertanyaan'] ?? 0) + 1;

        $data = [
            'urutan_pertanyaan' => $nextOrder,
            'isi_pertanyaan'   => $this->request->getPost('isi_pertanyaan'),
            'tipe_pertanyaan'  => $this->request->getPost('tipe_pertanyaan'),
            'pilihan_a'        => $this->request->getPost('pilihan_a'),
            'tipe_pilihan_a'   => $this->request->getPost('tipe_pilihan_a') ?: 'text',
            'pilihan_b'        => $this->request->getPost('pilihan_b'),
            'tipe_pilihan_b'   => $this->request->getPost('tipe_pilihan_b') ?: 'text',
            'pilihan_c'        => $this->request->getPost('pilihan_c'),
            'tipe_pilihan_c'   => $this->request->getPost('tipe_pilihan_c') ?: 'text',
            'pilihan_d'        => $this->request->getPost('pilihan_d'),
            'tipe_pilihan_d'   => $this->request->getPost('tipe_pilihan_d') ?: 'text',
            'pilihan_e'        => $this->request->getPost('pilihan_e'),
            'tipe_pilihan_e'   => $this->request->getPost('tipe_pilihan_e') ?: 'text',
            'jawaban_benar'    => $this->request->getPost('jawaban_benar'),
            'status_pertanyaan'=> $this->request->getPost('status_pertanyaan'),
            'tanggal_dibuat'   => date('Y-m-d H:i:s'),
        ];

        // Handle main question image
        $data['gambar_pertanyaan'] = $this->handleUpload('gambar_pertanyaan');

        // Handle option images and text cleanup
        foreach (['a', 'b', 'c', 'd', 'e'] as $o) {
            $type = $data["tipe_pilihan_$o"];
            $imgField = "gambar_pilihan_$o";
            
            if ($type === 'gambar') {
                $data[$imgField] = $this->handleUpload($imgField);
            } else {
                $data[$imgField] = null;
            }
        }

        try {
            $this->questionModel->insert($data);
            return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan pertanyaan: ' . $e->getMessage());
        }
    }

    public function update($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $this->questionModel->setSession($session);
        $id = $this->request->getPost('id_pertanyaan');
        
        $data = [
            'isi_pertanyaan'   => $this->request->getPost('isi_pertanyaan'),
            'tipe_pertanyaan'  => $this->request->getPost('tipe_pertanyaan'),
            'pilihan_a'        => $this->request->getPost('pilihan_a'),
            'tipe_pilihan_a'   => $this->request->getPost('tipe_pilihan_a'),
            'pilihan_b'        => $this->request->getPost('pilihan_b'),
            'tipe_pilihan_b'   => $this->request->getPost('tipe_pilihan_b'),
            'pilihan_c'        => $this->request->getPost('pilihan_c'),
            'tipe_pilihan_c'   => $this->request->getPost('tipe_pilihan_c'),
            'pilihan_d'        => $this->request->getPost('pilihan_d'),
            'tipe_pilihan_d'   => $this->request->getPost('tipe_pilihan_d'),
            'pilihan_e'        => $this->request->getPost('pilihan_e'),
            'tipe_pilihan_e'   => $this->request->getPost('tipe_pilihan_e'),
            'jawaban_benar'    => $this->request->getPost('jawaban_benar'),
            'status_pertanyaan'=> $this->request->getPost('status_pertanyaan'),
            'tanggal_diubah'   => date('Y-m-d H:i:s'),
        ];

        // Handle uploads and type-based cleanup
        if ($img = $this->handleUpload('gambar_pertanyaan')) {
            $data['gambar_pertanyaan'] = $img;
        }

        foreach (['a', 'b', 'c', 'd', 'e'] as $o) {
            $type = $data["tipe_pilihan_$o"];
            $imgField = "gambar_pilihan_$o";
            
            if ($type === 'gambar') {
                if ($img = $this->handleUpload($imgField)) {
                    $data[$imgField] = $img;
                }
            } else {
                $data[$imgField] = null;
            }
        }

        try {
            $this->questionModel->update($id, $data);
            return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui pertanyaan: ' . $e->getMessage());
        }
    }

    public function delete($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $this->questionModel->setSession($session);
        $id = $this->request->getPost('id');
        try {
            $question = $this->questionModel->find($id);
            if ($question) {
                $order = $question['urutan_pertanyaan'];
                $this->questionModel->delete($id);
                
                // Shift subsequent questions down to fill the gap
                $this->questionModel->where('urutan_pertanyaan >', $order)
                                    ->set('urutan_pertanyaan', 'urutan_pertanyaan - 1', false)
                                    ->update();
            }
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function reorder($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $this->questionModel->setSession($session);
        $id = $this->request->getPost('id');
        $direction = $this->request->getPost('direction'); // 'up', 'down', or numeric target
        
        $currentQuestion = $this->questionModel->find($id);
        if (!$currentQuestion) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'message' => 'Question not found']);
        }

        $currentOrder = (int)$currentQuestion['urutan_pertanyaan'];
        $allQuestions = $this->questionModel->orderBy('urutan_pertanyaan', 'ASC')->findAll();
        $total = count($allQuestions);

        if ($direction === 'up' && $currentOrder > 1) {
            $targetOrder = $currentOrder - 1;
        } elseif ($direction === 'down' && $currentOrder < $total) {
            $targetOrder = $currentOrder + 1;
        } elseif (is_numeric($direction)) {
            $targetOrder = (int)$direction;
        } else {
            return $this->response->setJSON(['ok' => false, 'message' => 'Invalid direction or boundary reached']);
        }

        if ($targetOrder < 1) $targetOrder = 1;
        if ($targetOrder > $total) $targetOrder = $total;

        if ($targetOrder === $currentOrder) {
            return $this->response->setJSON(['ok' => true]);
        }

        try {
            if ($targetOrder < $currentOrder) {
                // Moving up: shift others down
                $this->questionModel->where('urutan_pertanyaan >=', $targetOrder)
                                    ->where('urutan_pertanyaan <', $currentOrder)
                                    ->set('urutan_pertanyaan', 'urutan_pertanyaan + 1', false)
                                    ->update();
            } else {
                // Moving down: shift others up
                $this->questionModel->where('urutan_pertanyaan >', $currentOrder)
                                    ->where('urutan_pertanyaan <=', $targetOrder)
                                    ->set('urutan_pertanyaan', 'urutan_pertanyaan - 1', false)
                                    ->update();
            }

            $this->questionModel->update($id, ['urutan_pertanyaan' => $targetOrder]);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function toggleStatus($session = 1)
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $this->questionModel->setSession($session);
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        try {
            $this->questionModel->update($id, ['status_pertanyaan' => $status]);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    private function handleUpload(string $fieldName): ?string
    {
        $img = $this->request->getFile($fieldName);
        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'public/uploads/questions', $newName);
            return 'uploads/questions/' . $newName;
        }
        return null;
    }
}
