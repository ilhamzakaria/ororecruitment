<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Throwable;

class UserAccountStore
{
    /**
     * @return array<string, mixed>|null
     */
    public function authenticate(string $idUser, string $password, string $sessionId = null): ?array
    {
        $idUser = $this->normalizeIdUser($idUser);
        if ($idUser === '' || $password === '') {
            return null;
        }

        $db = $this->db();
        if ($db === null) {
            return null;
        }

        try {
            // 1. Check in users table
            $userAuth = $db->table('users')
                ->groupStart()
                    ->where('id', $idUser)
                    ->orWhere('username', $idUser)
                ->groupEnd()
                ->get()
                ->getRowArray();

            if (!$userAuth) {
                return null;
            }

            // 2. Check password
            if (!$this->passwordMatches($password, (string) ($userAuth['password'] ?? ''))) {
                return null;
            }

            // 3. Check status
            if (($userAuth['status'] ?? 'nonaktif') !== 'aktif') {
                throw new \Exception('Akun Anda belum aktif atau dinonaktifkan oleh HRD.');
            }

            if (($userAuth['status_pengguna'] ?? 'aktif') === 'eliminasi') {
                throw new \Exception('Akun Anda telah dieliminasi dari sistem.');
            }

            // 4. Fetch profile data based on role
            $profile = null;
            if ($userAuth['role'] === 'pegawai') {
                $profile = $db->table('pegawai')->where('id_user', $userAuth['id'])->get()->getRowArray();
            } elseif ($userAuth['role'] === 'hrd') {
                $profile = $db->table('hrd')->where('id_hrd', $userAuth['id'])->get()->getRowArray();
            } else {
                $profile = $db->table('manager')->where('id_manager', $userAuth['id'])->get()->getRowArray();
            }

            if (!$profile) {
                return null;
            }

            $publicUser = $this->publicUser(array_merge($userAuth, $profile), $userAuth['role']);
            
            // 5. Update session_id if provided
            if ($sessionId) {
                $db->table('users')->where('id', $userAuth['id'])->update(['session_id' => $sessionId]);
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
        $db = $this->db();
        if (!$db) return;

        $db->table('users')->where('id', $idUser)->update(['session_id' => null]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(string $idUser): ?array
    {
        $idUser = $this->normalizeIdUser($idUser);
        $db = $this->db();
        if ($db === null) {
            return null;
        }

        try {
            $userAuth = $db->table('users')->where('id', $idUser)->get()->getRowArray();
            if (!$userAuth) return null;

            if ($userAuth['role'] === 'pegawai') {
                $profile = $db->table('pegawai')->where('id_user', $userAuth['id'])->get()->getRowArray();
            } elseif ($userAuth['role'] === 'hrd') {
                $profile = $db->table('hrd')->where('id_hrd', $userAuth['id'])->get()->getRowArray();
            } else {
                $profile = $db->table('manager')->where('id_manager', $userAuth['id'])->get()->getRowArray();
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
        $db = $this->db();
        if ($db === null) return [];

        try {
            return $db->table('hrd')
                ->select('hrd.*, users.username, users.status, users.session_id')
                ->join('users', 'users.id = hrd.id_hrd')
                ->where('users.role', 'hrd')
                ->orderBy('hrd.nama', 'ASC')
                ->get()
                ->getResultArray();
        } catch (Throwable) {
            return [];
        }
    }

    public function listPegawai(): array
    {
        $db = $this->db();
        if ($db === null) return [];

        try {
            return $db->table('pegawai')
                ->select('pegawai.*, users.username, users.status, users.status_pengguna, users.session_id')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('users.role', 'pegawai')
                ->where('users.status_pengguna', 'aktif')
                ->orderBy('pegawai.nama', 'ASC')
                ->get()
                ->getResultArray();
        } catch (Throwable) {
            return [];
        }
    }

    public function listEliminasi(): array
    {
        $db = $this->db();
        if ($db === null) return [];

        try {
            $pegawai = $db->table('pegawai')
                ->select('pegawai.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('users.status_pengguna', 'eliminasi')
                ->get()
                ->getResultArray();

            $hrd = $db->table('hrd')
                ->select('hrd.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = hrd.id_hrd')
                ->where('users.status_pengguna', 'eliminasi')
                ->get()
                ->getResultArray();

            $manager = $db->table('manager')
                ->select('manager.*, users.username, users.status, users.status_pengguna, users.role, users.tanggal_eliminasi, users.dieliminasi_oleh')
                ->join('users', 'users.id = manager.id_manager')
                ->where('users.status_pengguna', 'eliminasi')
                ->get()
                ->getResultArray();

            return array_merge($pegawai, $hrd, $manager);
        } catch (Throwable) {
            return [];
        }
    }

    public function listManager(): array
    {
        $db = $this->db();
        if ($db === null) return [];

        try {
            return $db->table('manager')
                ->select('manager.*, users.username, users.status, users.session_id')
                ->join('users', 'users.id = manager.id_manager')
                ->where('users.role', 'manager')
                ->orderBy('manager.nama', 'ASC')
                ->get()
                ->getResultArray();
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
        $db = $this->db();
        if ($db === null) return null;

        try {
            $row = $db->table('pegawai')
                ->select('pegawai.*, users.username, users.status')
                ->join('users', 'users.id = pegawai.id_user')
                ->where('pegawai.id_user', $idUser)
                ->get()
                ->getRowArray();

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
        $db = $this->db();
        if (!$db) return false;

        $db->transBegin();

        try {
            if ($role === 'hrd') {
                $count = $db->table('hrd')->countAllResults();
                $id = 'HRD' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                
                $db->table('users')->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => $data['password'],
                    'role'       => 'hrd',
                    'status'     => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $db->table('hrd')->insert([
                    'id_hrd' => $id,
                    'nama'   => $data['nama'],
                ]);
            } elseif ($role === 'manager') {
                $count = $db->table('manager')->countAllResults();
                $id = 'MGR' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                
                $db->table('users')->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => $data['password'],
                    'role'       => 'manager',
                    'status'     => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $db->table('manager')->insert([
                    'id_manager' => $id,
                    'nama'       => $data['nama'],
                ]);
            } else {
                $count = $db->table('pegawai')->countAllResults();
                $id = 'PGW' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                $db->table('users')->insert([
                    'id'         => $id,
                    'username'   => $data['username'],
                    'password'   => $data['password'],
                    'role'       => 'pegawai',
                    'status'     => 'nonaktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $db->table('pegawai')->insert([
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

    private function db(): ?BaseConnection
    {
        try {
            $db = db_connect();
            $db->initialize();
            return $db;
        } catch (Throwable) {
            return null;
        }
    }

    private function publicUser(array $user, string $role): array
    {
        return [
            'id_user'      => trim((string) ($user['id_user'] ?? $user['id_hrd'] ?? '')),
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
