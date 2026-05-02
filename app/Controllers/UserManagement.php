<?php

namespace App\Controllers;

use App\Libraries\UserAccountStore;
use Throwable;

class UserManagement extends BaseController
{
    private UserAccountStore $accounts;

    public function __construct()
    {
        $this->accounts = new UserAccountStore();
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
        $role = $this->request->getPost('role'); // We need role to check permission
        $status = $this->request->getPost('status');
        $authUser = $this->authUser();

        // HRD cannot toggle status of other HRD or Manager
        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'HRD tidak boleh mengubah status HRD lain atau Manager.']);
        }

        $db = db_connect();
        try {
            $db->table('users')->where('id', $id)->update(['status' => $status]);
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

        // HRD cannot delete other HRD or Manager
        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'HRD tidak boleh menghapus HRD lain atau Manager.']);
        }

        $db = db_connect();
        try {
            $db->table('users')->where('id', $id)->update([
                'status_pengguna' => 'eliminasi',
                'tanggal_eliminasi' => date('Y-m-d H:i:s'),
                'dieliminasi_oleh' => $authUser['username']
            ]);

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
        
        $db = db_connect();
        try {
            $db->table('users')->where('id', $id)->update([
                'status_pengguna' => 'aktif',
                'tanggal_eliminasi' => null,
                'dieliminasi_oleh' => null
            ]);

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

        // HRD cannot update other HRD or Manager
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

        $db = db_connect();
        $db->transBegin();
        try {
            $db->table('users')->where('id', $id)->update($authData);
            if ($role === 'hrd') {
                $db->table('hrd')->where('id_hrd', $id)->update($profileData);
            } elseif ($role === 'manager') {
                $db->table('manager')->where('id_manager', $id)->update($profileData);
            } else {
                $db->table('pegawai')->where('id_user', $id)->update($profileData);
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

        // HRD cannot add HRD or Manager
        if ($authUser['role'] === 'hrd' && ($role === 'hrd' || $role === 'manager')) {
            return redirect()->back()->with('error', 'HRD hanya boleh menambahkan Pegawai.');
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash((string)$this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        if ($role === 'pegawai') {
            $data['posisi'] = $this->request->getPost('posisi');
            $data['alamat'] = $this->request->getPost('alamat');
        }

        if ($this->accounts->register($data, $role)) {
            // Force active if added by HRD/Manager
            $db = db_connect();
            $db->table('users')->where('username', $data['username'])->update(['status' => 'aktif']);

            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Gagal menambahkan user.');
    }
}
