<?php

namespace App\Controllers;

use App\Libraries\InterviewMonitorStore;
use InvalidArgumentException;
use Throwable;

class Monitoring extends BaseController
{
    private InterviewMonitorStore $store;

    public function __construct()
    {
        $this->store = new InterviewMonitorStore();
    }

    public function dashboard()
    {
        helper('url');

        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $dashboard = $this->store->getDashboardData();

        $db = \Config\Database::connect();
        $sessionControl = $db->table('kontrol_sesi_pegawai')->get()->getResultArray();

        return view('hrd_dashboard', [
            'summary' => $dashboard['summary'],
            'sessions' => $dashboard['sessions'],
            'recentViolations' => $dashboard['recentViolations'],
            'answersReport' => $this->getAnswersReport(),
            'sessionControl' => $sessionControl,
            'updatedAt' => $dashboard['updatedAt'],
            'dashboardDataUrl' => site_url('dashboard-hrd/data'),
            'interviewUrl' => site_url('tes-interview'),
            'logoutUrl' => site_url('logout'),
            'authUser' => $this->authUser(),
        ]);
    }

    public function dashboardData()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'ok' => false,
                    'message' => 'Akses dashboard hanya untuk HRD atau Manager.',
                ]);
        }

        $db = \Config\Database::connect();
        $activityLogs = $db->table('log_aktivitas_tes')
            ->orderBy('waktu_lengkap', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        $sessionControl = $db->table('kontrol_sesi_pegawai')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(array_merge(
            $this->store->getDashboardData(),
            [
                'answersReport' => $this->getAnswersReport(),
                'activityLogs' => $activityLogs,
                'sessionControl' => $sessionControl,
            ]
        ));
    }

    private function getAnswersReport()
    {
        $db = \Config\Database::connect();
        
        // We need to find all employees across all 3 session answer tables
        $allPegawai = [];
        for ($i = 1; $i <= 3; $i++) {
            $emps = $db->table("jawaban_sesi_$i")
                ->select('id_pegawai, nama_pegawai, MAX(tanggal_menjawab) as terakhir')
                ->groupBy('id_pegawai, nama_pegawai')
                ->get()
                ->getResultArray();
            
            foreach ($emps as $e) {
                $id = $e['id_pegawai'];
                if (!isset($allPegawai[$id])) {
                    $allPegawai[$id] = $e;
                } else {
                    if ($e['terakhir'] > $allPegawai[$id]['terakhir']) {
                        $allPegawai[$id]['terakhir'] = $e['terakhir'];
                    }
                }
            }
        }

        // Sort by latest activity
        uasort($allPegawai, function($a, $b) {
            return strcmp($b['terakhir'], $a['terakhir']);
        });

        $report = [];
        foreach ($allPegawai as $emp) {
            $id = $emp['id_pegawai'];
            
            $totalSoalAll = 0;
            $totalDijawabAll = 0;
            $benarAll = 0;
            $salahAll = 0;
            $nilaiAkhirAll = 0;
            $allDetails = [];
            $sessionStats = [];

            for ($i = 1; $i <= 3; $i++) {
                $qTable = "pertanyaan_sesi_$i";
                $aTable = "jawaban_sesi_$i";

                $totalSoalSesi = $db->table($qTable)->where('status_pertanyaan', 'Aktif')->countAllResults();
                $answersSesi = $db->table("$aTable as j")
                    ->select('j.*, p.isi_pertanyaan, p.tipe_pertanyaan, p.gambar_pertanyaan, 
                             p.pilihan_a, p.pilihan_b, p.pilihan_c, p.pilihan_d, p.pilihan_e,
                             p.gambar_pilihan_a, p.gambar_pilihan_b, p.gambar_pilihan_c, p.gambar_pilihan_d, p.gambar_pilihan_e,
                             p.tipe_pilihan_a, p.tipe_pilihan_b, p.tipe_pilihan_c, p.tipe_pilihan_d, p.tipe_pilihan_e')
                    ->join("$qTable as p", 'p.id_pertanyaan = j.id_pertanyaan', 'left')
                    ->where('j.id_pegawai', $id)
                    ->orderBy('j.nomor_pertanyaan', 'ASC')
                    ->get()
                    ->getResultArray();

                $benarSesi = 0;
                $nilaiSesi = 0;
                foreach ($answersSesi as &$a) {
                    $a['sesi'] = $i; // add session info to detail
                    if ($a['status_jawaban'] === 'Benar') {
                        $benarSesi++;
                    }
                    $nilaiSesi += $a['nilai'];
                }

                $sessionStats["sesi_$i"] = [
                    'total_soal' => $totalSoalSesi,
                    'terjawab' => count($answersSesi),
                    'benar' => $benarSesi,
                    'salah' => count($answersSesi) - $benarSesi,
                    'nilai' => $nilaiSesi
                ];

                $totalSoalAll += $totalSoalSesi;
                $totalDijawabAll += count($answersSesi);
                $benarAll += $benarSesi;
                $salahAll += (count($answersSesi) - $benarSesi);
                $nilaiAkhirAll += $nilaiSesi;
                $allDetails = array_merge($allDetails, $answersSesi);
            }

            // Get latest status from peserta table
            $peserta = $db->table('peserta')
                ->where('id_user', $id)
                ->orderBy('updated_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            $report[] = [
                'id_pegawai' => $id,
                'nama_pegawai' => $emp['nama_pegawai'],
                'tanggal_tes' => $emp['terakhir'],
                'total_soal' => $totalSoalAll,
                'total_dijawab' => $totalDijawabAll,
                'benar' => $benarAll,
                'salah' => $salahAll,
                'nilai_akhir' => $nilaiAkhirAll,
                'status_tes' => $peserta['status'] ?? 'Selesai',
                'violations' => (int) ($peserta['violations_count'] ?? 0),
                'current_session' => $peserta['current_session'] ?? 1,
                'session_stats' => $sessionStats,
                'detail' => $allDetails
            ];
        }

        return $report;
    }

    public function recordEvent()
    {
        if (! $this->authAllows(['hrd', 'pegawai', 'manager'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'ok' => false,
                    'message' => 'Login diperlukan untuk menyimpan monitoring.',
                ]);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload) || $payload === []) {
            $payload = $this->request->getPost();
        }

        $authUser = $this->authUser();
        if (is_array($authUser)) {
            $payload['idUser'] = $payload['idUser'] ?? ($authUser['id_user'] ?? '');
            $payload['userRole'] = $payload['userRole'] ?? ($authUser['role'] ?? '');
        }

        try {
            $session = $this->store->recordEvent($payload);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'ok' => true,
                    'session' => $session,
                ]);
        } catch (InvalidArgumentException $exception) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'ok' => false,
                    'message' => $exception->getMessage(),
                ]);
        } catch (Throwable $exception) {
            log_message('error', 'Monitoring event gagal disimpan: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'ok' => false,
                    'message' => 'Event monitoring gagal disimpan.',
                ]);
        }
    }

    public function getActivityLogs()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false]);
        }

        $nama = $this->request->getGet('nama');
        $sesi = $this->request->getGet('sesi');
        $tanggal = $this->request->getGet('tanggal');
        $aktivitas = $this->request->getGet('aktivitas');

        $db = \Config\Database::connect();
        $builder = $db->table('log_aktivitas_tes');

        if ($nama) $builder->like('nama_pegawai', $nama);
        if ($sesi) $builder->where('nomor_sesi', $sesi);
        if ($tanggal) $builder->where('tanggal_aktivitas', $tanggal);
        if ($aktivitas) $builder->where('aktivitas', $aktivitas);

        $logs = $builder->orderBy('waktu_lengkap', 'DESC')->get()->getResultArray();

        return $this->response->setJSON(['ok' => true, 'logs' => $logs]);
    }

    public function openSession()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Unauthorized']);
        }

        $idPegawai = $this->request->getPost('idPegawai');
        $namaPegawai = $this->request->getPost('namaPegawai');
        $nomorSesi = (int) $this->request->getPost('nomorSesi');

        if (!$idPegawai || !$nomorSesi) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Data tidak lengkap']);
        }

        $db = \Config\Database::connect();
        $authUser = $this->authUser();

        $data = [
            'id_pegawai' => $idPegawai,
            'nama_pegawai' => $namaPegawai,
            'nomor_sesi' => $nomorSesi,
            'status_sesi' => 'dibuka',
            'dibuka_oleh' => $authUser['name'] ?? $authUser['id_user'],
            'role_pembuka' => $authUser['role'],
            'tanggal_dibuka' => date('Y-m-d'),
            'waktu_dibuka' => date('H:i:s'),
        ];

        $existing = $db->table('kontrol_sesi_pegawai')
            ->where('id_pegawai', $idPegawai)
            ->where('nomor_sesi', $nomorSesi)
            ->get()->getRowArray();

        if ($existing) {
            $db->table('kontrol_sesi_pegawai')
                ->where('id_kontrol', $existing['id_kontrol'])
                ->update($data);
        } else {
            $db->table('kontrol_sesi_pegawai')->insert($data);
        }

        // Also reset status_sesi_peserta to ensure timer starts fresh
        $sessionSetting = $db->table('pengaturan_sesi')->where('id_sesi', $nomorSesi)->get()->getRowArray();
        $duration = (int) ($sessionSetting['durasi_menit'] ?? 10);
        
        $statusData = [
            'status_sesi' => 'belum_mulai',
            'waktu_mulai' => null,
            'tanggal_selesai' => null,
            'waktu_sisa' => $duration * 60,
            'durasi_menit' => $duration
        ];

        $existsStatus = $db->table('status_sesi_peserta')
            ->where('id_pegawai', $idPegawai)
            ->where('nomor_sesi', $nomorSesi)
            ->get()->getRowArray();

        if ($existsStatus) {
            $db->table('status_sesi_peserta')
                ->where('id_status', $existsStatus['id_status'])
                ->update($statusData);
        } else {
            $statusData['id_pegawai'] = $idPegawai;
            $statusData['nomor_sesi'] = $nomorSesi;
            $db->table('status_sesi_peserta')->insert($statusData);
        }

        return $this->response->setJSON(['ok' => true, 'message' => "Sesi $nomorSesi dibuka untuk $namaPegawai"]);
    }

    public function bulkOpenSessions()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Unauthorized']);
        }

        $userIds = $this->request->getPost('userIds'); // Array of IDs
        $nomorSesi = (int) $this->request->getPost('nomorSesi');

        if (! $userIds || ! is_array($userIds) || ! $nomorSesi) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'message' => 'Data tidak lengkap atau format salah.']);
        }

        $db = \Config\Database::connect();
        $authUser = $this->authUser();
        $now = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $sessionSetting = $db->table('pengaturan_sesi')->where('id_sesi', $nomorSesi)->get()->getRowArray();
        $duration = (int) ($sessionSetting['durasi_menit'] ?? 10);

        $successCount = 0;
        foreach ($userIds as $idPegawai) {
            // Get employee name
            $pegawai = $db->table('users')->where('id', $idPegawai)->get()->getRowArray();
            if (!$pegawai) continue;
            
            $namaPegawai = $pegawai['name'] ?? 'User';

            // 1. Update/Insert kontrol_sesi_pegawai
            $existsKontrol = $db->table('kontrol_sesi_pegawai')
                ->where('id_pegawai', $idPegawai)
                ->where('nomor_sesi', $nomorSesi)
                ->get()->getRowArray();

            $kontrolData = [
                'status_sesi' => 'dibuka',
                'dibuka_oleh' => $authUser['name'] ?? 'System',
                'role_pembuka' => $authUser['role'] ?? 'hrd',
                'tanggal_dibuka' => $date,
                'waktu_dibuka' => $time,
                'nama_pegawai' => $namaPegawai
            ];

            if ($existsKontrol) {
                $db->table('kontrol_sesi_pegawai')->where('id_kontrol', $existsKontrol['id_kontrol'])->update($kontrolData);
            } else {
                $kontrolData['id_pegawai'] = $idPegawai;
                $kontrolData['nomor_sesi'] = $nomorSesi;
                $db->table('kontrol_sesi_pegawai')->insert($kontrolData);
            }

            // 2. Reset status_sesi_peserta
            $statusData = [
                'status_sesi' => 'belum_mulai',
                'waktu_mulai' => null,
                'tanggal_selesai' => null,
                'waktu_sisa' => $duration * 60,
                'durasi_menit' => $duration
            ];

            $existsStatus = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $idPegawai)
                ->where('nomor_sesi', $nomorSesi)
                ->get()->getRowArray();

            if ($existsStatus) {
                $db->table('status_sesi_peserta')->where('id_status', $existsStatus['id_status'])->update($statusData);
            } else {
                $statusData['id_pegawai'] = $idPegawai;
                $statusData['nomor_sesi'] = $nomorSesi;
                $db->table('status_sesi_peserta')->insert($statusData);
            }
            
            $successCount++;
        }

        return $this->response->setJSON([
            'ok' => true, 
            'message' => "Sesi $nomorSesi berhasil dibuka untuk $successCount pegawai terpilih."
        ]);
    }

    public function bulkCloseSessions()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Unauthorized']);
        }

        $userIds = $this->request->getPost('userIds');
        $nomorSesi = (int) $this->request->getPost('nomorSesi');

        if (! $userIds || ! is_array($userIds) || ! $nomorSesi) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'message' => 'Data tidak lengkap atau format salah.']);
        }

        $db = \Config\Database::connect();
        $successCount = 0;

        foreach ($userIds as $idPegawai) {
            $existingControl = $db->table('kontrol_sesi_pegawai')
                ->where('id_pegawai', $idPegawai)
                ->where('nomor_sesi', $nomorSesi)
                ->get()
                ->getRowArray();

            if (! $existingControl) {
                continue;
            }

            $db->table('kontrol_sesi_pegawai')
                ->where('id_kontrol', $existingControl['id_kontrol'])
                ->update([
                    'status_sesi' => 'belum_dibuka',
                ]);

            $successCount++;
        }

        return $this->response->setJSON([
            'ok' => true,
            'message' => "Sesi $nomorSesi berhasil ditutup untuk $successCount pegawai terpilih."
        ]);
    }

    public function manageSessions()
    {
        helper('url');
        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $db = \Config\Database::connect();
        
        // Get all employees
        $employees = $db->table('users')
            ->where('role', 'pegawai')
            ->get()->getResultArray();

        // Get all controls to display current status
        $controls = $db->table('kontrol_sesi_pegawai')->get()->getResultArray();

        return view('manage_sessions', [
            'employees' => $employees,
            'controls' => $controls,
            'authUser' => $this->authUser(),
        ]);
    }

    public function unblockSession()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Unauthorized']);
        }

        $sessionId = $this->request->getPost('sessionId');
        if (! $sessionId) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'message' => 'Missing session ID']);
        }

        $db = \Config\Database::connect();
        $db->table('peserta')->where('session_id', $sessionId)->update([
            'is_blocked' => 0,
            'status' => 'active',
            'violations_count' => 0,
            'tab_switches' => 0,
            'last_message' => 'Blokir dibuka oleh Manager/HRD',
        ]);

        return $this->response->setJSON(['ok' => true]);
    }
}
