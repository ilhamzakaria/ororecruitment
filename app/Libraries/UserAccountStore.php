<?php

namespace App\Libraries;

use App\Models\UserModel;
use App\Models\PegawaiModel;
use App\Models\HrdModel;
use App\Models\ManagerModel;
use Throwable;

class UserAccountStore
{
    protected UserModel $userModel;
    protected PegawaiModel $pegawaiModel;
    protected HrdModel $hrdModel;
    protected ManagerModel $managerModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->hrdModel = new HrdModel();
        $this->managerModel = new ManagerModel();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function authenticate(string $idUser, string $password, string $sessionId = null): ?array
    {
        $idUser = $this->normalizeIdUser($idUser);
        if ($idUser === '' || $password === '') {
            return null;
        }

        try {
            $userAuth = $this->userModel->groupStart()
                    ->where('id', $idUser)
                    ->orWhere('username', $idUser)
                ->groupEnd()
                ->first();

            if (!$userAuth) {
                return null;
            }

            if (!$this->passwordMatches($password, (string) ($userAuth['password'] ?? ''))) {
                return null;
            }

            if (($userAuth['status'] ?? 'nonaktif') !== 'aktif') {
                throw new \Exception('Akun Anda belum aktif atau dinonaktifkan oleh HRD.');
            }

            if (($userAuth['status_pengguna'] ?? 'aktif') === 'eliminasi') {
                throw new \Exception('Akun Anda telah dieliminasi dari sistem.');
            }

            $profile = null;
            if ($userAuth['role'] === 'pegawai') {
                $profile = $this->pegawaiModel->find($userAuth['id']);
            } elseif ($userAuth['role'] === 'hrd') {
                $profile = $this->hrdModel->find($userAuth['id']);
            } else {
                $profile = $this->managerModel->find($userAuth['id']);
            }

            if (!$profile) {
                return null;
            }

            $publicUser = $this->publicUser(array_merge($userAuth, $profile), $userAuth['role']);
            
            if ($sessionId) {
                $this->userModel->update($userAuth['id'], ['session_id' => $sessionId]);
            }
            
            return $publicUser;

        } catch (\Exception $e) {
            throw $e;
        } catch (Throwable) {
            return null;
        }
    }

    public function logout(string $idUser, string $role): void
    {
        $this->userModel->update($idUser, ['session_id' => null]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(string $idUser): ?array
    {
        $idUser = $this->normalizeIdUser($idUser);
        try {
            $userAuth = $this->userModel->find($idUser);
            if (!$userAuth) return null;

            if ($userAuth['role'] === 'pegawai') {
                $profile = $this->pegawaiModel->find($userAuth['id']);
            } elseif ($userAuth['role'] === 'hrd') {
                $profile = $this->hrdModel->find($userAuth['id']);
            } else {
                $profile = $this->managerModel->find($userAuth['id']);
            }

            if (!$profile) return null;

            return $this->publicUser(array_merge($userAuth, $profile), $userAuth['role']);

        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listHrd(): array
    {
        try {
            return $this->hrdModel
                ->select('hrd.*, users.username, users.status, users.session_id')
                ->join('users', 'users.id = hrd.id_hrd')
                ->where('users.role', 'hrd')
                ->orderBy('hrd.nama', 'ASC')
                ->findAll();
        } catch (Throwable) {
            return [];
        }
    }

    public function listPegawai(): array
    {
        try {
            return $this->pegawaiModel
                ->select('pegawai.*, users.username, users.status, users.status_pengguna, users.session_id')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('users.role', 'pegawai')
                ->where('users.status_pengguna', 'aktif')
                ->orderBy('pegawai.nama', 'ASC')
                ->findAll();
        } catch (Throwable) {
            return [];
        }
    }

    public function listEliminasi(): array
    {
        try {
            $pegawai = $this->pegawaiModel
                ->select('pegawai.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('users.status_pengguna', 'eliminasi')
                ->findAll();

            $hrd = $this->hrdModel
                ->select('hrd.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = hrd.id_hrd')
                ->where('users.status_pengguna', 'eliminasi')
                ->findAll();

            $manager = $this->managerModel
                ->select('manager.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = manager.id_manager')
                ->where('users.status_pengguna', 'eliminasi')
                ->findAll();

            return array_merge($pegawai, $hrd, $manager);
        } catch (Throwable) {
            return [];
        }
    }

    public function listManager(): array
    {
        try {
            return $this->managerModel
                ->select('manager.*, users.username, users.status, users.session_id')
                ->join('users', 'users.id = manager.id_manager')
                ->where('users.role', 'manager')
                ->orderBy('manager.nama', 'ASC')
                ->findAll();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<string, string>|null
     */
    public function findPegawaiProfileByIdUser(string $idUser): ?array
    {
        $idUser = $this->normalizeIdUser($idUser);
        try {
            $row = $this->pegawaiModel
                ->select('pegawai.*, users.username, users.status')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('pegawai.id_user', $idUser)
                ->first();

            if (!$row) return null;

            return [
                'id_user'      => (string) ($row['id_user'] ?? ''),
                'name'         => trim((string) ($row['nama'] ?? '')),
                'alamat'       => trim((string) ($row['alamat'] ?? '')),
                'positionName' => trim((string) ($row['posisi'] ?? '')),
                'username'     => trim((string) ($row['username'] ?? '')),
                'status'       => $row['status'] ?? 'nonaktif',
            ];
        } catch (Throwable) {
            return null;
        }
    }

    public function register(array $data, string $role): bool
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if ($role === 'hrd') {
                $count = $this->hrdModel->countAllResults();
                $id = 'HRD' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                
                $this->userModel->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role'       => 'hrd',
                    'status'     => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->hrdModel->insert([
                    'id_hrd' => $id,
                    'nama'   => $data['nama'],
                ]);
            } elseif ($role === 'manager') {
                $count = $this->managerModel->countAllResults();
                $id = 'MGR' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                
                $this->userModel->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role'       => 'manager',
                    'status'     => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->managerModel->insert([
                    'id_manager' => $id,
                    'nama'       => $data['nama'],
                ]);
            } else {
                $count = $this->pegawaiModel->countAllResults();
                $id = 'PGW' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                $this->userModel->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role'       => 'pegawai',
                    'status'     => 'nonaktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->pegawaiModel->insert([
                    'id_user' => $id,
                    'nama'    => $data['nama'],
                    'posisi'  => $data['posisi'] ?? '',
                    'alamat'  => $data['alamat'] ?? '',
                ]);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return false;
            }

            $db->transCommit();
            return true;

        } catch (Throwable) {
            $db->transRollback();
            return false;
        }
    }

    private function publicUser(array $user, string $role): array
    {
        return [
            'id_user'      => trim((string) ($user['id_user'] ?? $user['id_hrd'] ?? $user['id_manager'] ?? '')),
            'name'         => trim((string) ($user['nama'] ?? '')),
            'role'         => $role,
            'positionName' => trim((string) ($user['posisi'] ?? '')),
            'username'     => trim((string) ($user['username'] ?? '')),
            'session_id'   => $user['session_id'] ?? null,
        ];
    }

    private function passwordMatches(string $inputPassword, string $storedPassword): bool
    {
        $storedPassword = trim($storedPassword);
        if ($storedPassword === '') return false;

        if (str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$2a$')) {
            return password_verify($inputPassword, $storedPassword);
        }

        return hash_equals($storedPassword, $inputPassword);
    }

    private function normalizeIdUser(string $idUser): string
    {
        return strtoupper(trim($idUser));
    }
}
