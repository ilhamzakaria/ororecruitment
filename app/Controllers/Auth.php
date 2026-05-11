<?php

namespace App\Controllers;

use App\Libraries\UserAccountStore;

class Auth extends BaseController
{
    private UserAccountStore $accounts;

    public function __construct()
    {
        $this->accounts = new UserAccountStore();
    }

    public function login()
    {
        helper('url');

        $session = service('session');
        $user = $session->get('auth_user');
        if (is_array($user)) {
            return $this->redirectByRole($user);
        }

        return view('login', [
            'error' => $session->getFlashdata('error'),
            'lastIdUser' => $session->getFlashdata('last_id_user') ?? '',
            'loginUrl' => site_url('login'),
        ]);
    }

    public function register()
    {
        return view('register');
    }

    public function attemptRegister()
    {
        $role = $this->request->getPost('role');
        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $data = [
            'nama'     => $nama,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        if ($role === 'pegawai') {
            $data['posisi'] = $this->request->getPost('posisi');
            $data['alamat'] = $this->request->getPost('alamat');
        }

        if ($this->accounts->register($data, $role)) {
            $msg = ($role === 'pegawai') ? 'Registrasi berhasil! Silakan tunggu verifikasi oleh HRD.' : 'Registrasi HRD berhasil! Silakan login.';
            return redirect()->to(site_url('login'))->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Registrasi gagal. Silakan coba lagi.');
    }

    public function attemptLogin()
    {
        helper('url');
        $session = service('session');

        $idUser = (string) $this->request->getPost('id_user');
        $password = (string) $this->request->getPost('password');
        
        try {
            $user = $this->accounts->authenticate($idUser, $password, session_id());

            if ($user === null) {
                return redirect()
                    ->to(site_url('login'))
                    ->with('error', 'ID user atau password tidak sesuai.')
                    ->with('last_id_user', strtoupper(trim($idUser)));
            }

            /*
            // Single login enforcement for pegawai
            if ($user['role'] === 'pegawai') {
                // If there's already a session_id and it's different from current
                if ($user['session_id'] !== null && $user['session_id'] !== session_id()) {
                    return redirect()
                        ->to(site_url('login'))
                        ->with('error', 'Akun ini sedang login di perangkat lain. Hanya boleh 1 login aktif.')
                        ->with('last_id_user', strtoupper(trim($idUser)));
                }
            }
            */

            $session->regenerate(true);
            $session->set('auth_user', $user);

            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', $e->getMessage())
                ->with('last_id_user', strtoupper(trim($idUser)));
        }
    }

    public function logout()
    {
        helper('url');
        $session = service('session');
        $user = $session->get('auth_user');

        if ($user) {
            $this->accounts->logout($user['id_user'], $user['role']);
        }

        $session->destroy();

        return redirect()->to(site_url('login'));
    }

    /**
     * @param array<string, mixed> $user
     */
    private function redirectByRole(array $user)
    {
        helper('url');

        if (in_array(($user['role'] ?? ''), ['hrd', 'manager'])) {
            return redirect()->to(site_url('dashboard-hrd'));
        }

        return redirect()->to(site_url('dashboard-user'));
    }
}
