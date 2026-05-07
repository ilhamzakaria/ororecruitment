<?php

namespace App\Controllers;

use App\Models\SessionQuestionModel;
use App\Models\SessionAnswerModel;
use Throwable;

class QuestionManagement extends BaseController
{
    protected SessionQuestionModel $questionModel;
    protected SessionAnswerModel $answerModel;

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
        $this->questionModel->autoInitializeOrder();

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
        
        $data = [
            'urutan_pertanyaan' => $this->questionModel->getNextOrder(),
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

        // Handle option images
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

        // Handle uploads
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
            $this->questionModel->deleteAndShift($id);
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
        $direction = $this->request->getPost('direction');
        
        try {
            if ($this->questionModel->reorder($id, $direction)) {
                return $this->response->setJSON(['ok' => true]);
            }
            return $this->response->setJSON(['ok' => false, 'message' => 'Gagal mengubah urutan.']);
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
