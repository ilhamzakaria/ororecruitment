<?php

namespace App\Controllers;

use App\Libraries\UserAccountStore;
use App\Models\UserModel;
use App\Models\PegawaiModel;
use App\Models\HrdModel;
use App\Models\ManagerModel;
use Throwable;

class UserManagement extends BaseController
{
    private UserAccountStore $accounts;
    protected UserModel $userModel;
    protected PegawaiModel $pegawaiModel;
    protected HrdModel $hrdModel;
    protected ManagerModel $managerModel;

    public function __construct()
    {
        $this->accounts = new UserAccountStore();
        $this->userModel = new UserModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->hrdModel = new HrdModel();
        $this->managerModel = new ManagerModel();
    }

    public function index()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $authUser = $this->authUser();
        
        return view('hrd_manage_users', [
            'pegawai' => $this->accounts->listPegawai(),
            'hrd' => $this->accounts->listHrd(),
            'manager' => ($authUser['role'] === 'manager') ? $this->accounts->listManager() : [],
            'eliminasi' => $this->accounts->listEliminasi(),
            'authUser' => $authUser,
            'logoutUrl' => site_url('logout'),
        ]);
    }

    public function toggleStatus()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $authUser = $this->authUser();

        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'HRD tidak boleh mengubah status HRD lain atau Manager.']);
        }

        try {
            $this->userModel->setStatus($id, $status);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        $role = $this->request->getPost('role');
        $authUser = $this->authUser();

        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'HRD tidak boleh menghapus HRD lain atau Manager.']);
        }

        try {
            $this->userModel->eliminate($id, $authUser['username']);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function restore()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        
        try {
            $this->userModel->restore($id);
            return $this->response->setJSON(['ok' => true]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $id = $this->request->getPost('id');
        $role = $this->request->getPost('role');
        $authUser = $this->authUser();

        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return redirect()->back()->with('error', 'HRD tidak boleh mengedit HRD lain atau Manager.');
        }
        
        $authData = [
            'username' => $this->request->getPost('username'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $authData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $profileData = [
            'nama' => $this->request->getPost('nama'),
        ];

        if ($role === 'pegawai') {
            $profileData['posisi'] = $this->request->getPost('posisi');
            $profileData['alamat'] = $this->request->getPost('alamat');
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->userModel->update($id, $authData);
            if ($role === 'hrd') {
                $this->hrdModel->update($id, $profileData);
            } elseif ($role === 'manager') {
                $this->managerModel->update($id, $profileData);
            } else {
                $this->pegawaiModel->update($id, $profileData);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal memperbarui data.');
            }
            $db->transCommit();
            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } catch (Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function add()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
        }

        $role = $this->request->getPost('role');
        $authUser = $this->authUser();

        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return redirect()->back()->with('error', 'HRD hanya boleh menambahkan Pegawai.');
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => (string)$this->request->getPost('password'),
        ];

        if ($role === 'pegawai') {
            $data['posisi'] = $this->request->getPost('posisi');
            $data['alamat'] = $this->request->getPost('alamat');
        }

        if ($this->accounts->register($data, $role)) {
            // Force active if added by HRD/Manager
            $this->userModel->where('username', $data['username'])->set(['status' => 'aktif'])->update();

            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Gagal menambahkan user.');
    }
}
