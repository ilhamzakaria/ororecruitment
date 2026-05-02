<?php

namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\AnswerModel;
use Throwable;

class QuestionManagement extends BaseController
{
    protected $questionModel;
    protected $answerModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->answerModel = new AnswerModel();
    }

    public function index()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        return view('hrd_manage_questions', [
            'questions' => $this->questionModel->findAll(),
            'authUser'  => $this->authUser(),
            'logoutUrl' => site_url('logout'),
        ]);
    }

    public function add()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $authUser = $this->authUser();
        
        $data = [
            'isi_pertanyaan'   => $this->request->getPost('isi_pertanyaan'),
            'tipe_pertanyaan'  => $this->request->getPost('tipe_pertanyaan'),
            'pilihan_a'        => $this->request->getPost('pilihan_a'),
            'pilihan_b'        => $this->request->getPost('pilihan_b'),
            'pilihan_c'        => $this->request->getPost('pilihan_c'),
            'pilihan_d'        => $this->request->getPost('pilihan_d'),
            'jawaban_benar'    => $this->request->getPost('jawaban_benar'),
            'status_pertanyaan'=> $this->request->getPost('status_pertanyaan'),
            'dibuat_oleh'      => $authUser['id_user'] ?? 'system',
            'role_pembuat'     => $authUser['role'] ?? 'system',
            'tanggal_dibuat'   => date('Y-m-d H:i:s'),
        ];

        // Handle image upload
        $img = $this->request->getFile('gambar_pertanyaan');
        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'public/uploads/questions', $newName);
            $data['gambar_pertanyaan'] = 'uploads/questions/' . $newName;
        }

        try {
            $this->questionModel->insert($data);
            return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan pertanyaan: ' . $e->getMessage());
        }
    }

    public function update()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $id = $this->request->getPost('id_pertanyaan');
        
        $data = [
            'isi_pertanyaan'   => $this->request->getPost('isi_pertanyaan'),
            'tipe_pertanyaan'  => $this->request->getPost('tipe_pertanyaan'),
            'pilihan_a'        => $this->request->getPost('pilihan_a'),
            'pilihan_b'        => $this->request->getPost('pilihan_b'),
            'pilihan_c'        => $this->request->getPost('pilihan_c'),
            'pilihan_d'        => $this->request->getPost('pilihan_d'),
            'jawaban_benar'    => $this->request->getPost('jawaban_benar'),
            'status_pertanyaan'=> $this->request->getPost('status_pertanyaan'),
            'tanggal_diubah'   => date('Y-m-d H:i:s'),
        ];

        // Handle image upload
        $img = $this->request->getFile('gambar_pertanyaan');
        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(ROOTPATH . 'public/uploads/questions', $newName);
            $data['gambar_pertanyaan'] = 'uploads/questions/' . $newName;
        }

        try {
            $this->questionModel->update($id, $data);
            return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui pertanyaan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        try {
            $this->questionModel->delete($id);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function toggleStatus()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        try {
            $this->questionModel->update($id, ['status_pertanyaan' => $status]);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }
}
