<?php

namespace App\Controllers;

use App\Libraries\InterviewMonitorStore;
use App\Models\ParticipantModel;
use App\Models\SessionAnswerModel;
use App\Models\SessionQuestionModel;
use InvalidArgumentException;
use Throwable;

class Monitoring extends BaseController
{
    private InterviewMonitorStore $store;
    protected ParticipantModel $participantModel;
    protected SessionAnswerModel $sessionAnswerModel;
    protected SessionQuestionModel $sessionQuestionModel;

    public function __construct()
    {
        $this->store = new InterviewMonitorStore();
        $this->participantModel = new ParticipantModel();
        $this->sessionAnswerModel = new SessionAnswerModel();
        $this->sessionQuestionModel = new SessionQuestionModel();
    }

    public function dashboard()
    {
        helper('url');

        if (! $this->authAllows(['hrd', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $dashboard = $this->store->getDashboardData();

        return view('hrd_dashboard', [
            'summary' => $dashboard['summary'],
            'sessions' => $dashboard['sessions'],
            'recentViolations' => $dashboard['recentViolations'],
            'answersReport' => $this->getAnswersReport(),
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

        return $this->response->setJSON(array_merge(
            $this->store->getDashboardData(),
            ['answersReport' => $this->getAnswersReport()]
        ));
    }

    private function getAnswersReport()
    {
        // We need to find all employees across all 3 session answer tables
        $allPegawai = [];
        for ($i = 1; $i <= 3; $i++) {
            $this->sessionAnswerModel->setSession($i);
            $table = $this->sessionAnswerModel->getTable();

            // Defensive check
            $db = \Config\Database::connect();
            if (!$db->tableExists($table)) {
                continue;
            }

            $emps = $this->sessionAnswerModel
                ->select("$table.id_pegawai, $table.nama_pegawai, MAX($table.tanggal_menjawab) as terakhir")
                ->groupBy("$table.id_pegawai, $table.nama_pegawai")
                ->findAll();
            
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

                // Check if table exists to avoid crash
                $db = \Config\Database::connect();
                if (!$db->tableExists($aTable) || !$db->tableExists($qTable)) {
                    $sessionStats["sesi_$i"] = [
                        'total_soal' => 0,
                        'terjawab' => 0,
                        'benar' => 0,
                        'salah' => 0,
                        'nilai' => 0
                    ];
                    continue;
                }

                $totalSoalSesi = $this->sessionQuestionModel->setSession($i)->where('status_pertanyaan', 'Aktif')->countAllResults();
                
                $this->sessionAnswerModel->setSession($i);
                $answersSesi = $this->sessionAnswerModel
                    ->select("$aTable.*, p.isi_pertanyaan, p.tipe_pertanyaan, p.gambar_pertanyaan, 
                             p.pilihan_a, p.pilihan_b, p.pilihan_c, p.pilihan_d, p.pilihan_e,
                             p.gambar_pilihan_a, p.gambar_pilihan_b, p.gambar_pilihan_c, p.gambar_pilihan_d, p.gambar_pilihan_e,
                             p.tipe_pilihan_a, p.tipe_pilihan_b, p.tipe_pilihan_c, p.tipe_pilihan_d, p.tipe_pilihan_e")
                    ->join("$qTable as p", "p.id_pertanyaan = $aTable.id_pertanyaan", 'left')
                    ->where("$aTable.id_pegawai", $id)
                    ->orderBy("$aTable.nomor_pertanyaan", 'ASC')
                    ->findAll();

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
            $peserta = $this->participantModel
                ->where('id_user', $id)
                ->orderBy('updated_at', 'DESC')
                ->first();

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
                'current_session' => $peserta['current_session'] ?? 1,
                'session_stats' => $sessionStats,
                'detail' => $allDetails
            ];
        }

        return $report;
    }

    public function recordEvent()
    {
        if (! $this->authAllows(['hrd', 'pegawai'])) {
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

    public function unblockSession()
    {
        if (! $this->authAllows(['hrd', 'manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Unauthorized']);
        }

        $sessionId = $this->request->getPost('sessionId');
        if (! $sessionId) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'message' => 'Missing session ID']);
        }

        $this->participantModel->where('session_id', $sessionId)->set([
            'is_blocked' => 0,
            'status' => 'active',
            'violations_count' => 0,
            'tab_switches' => 0,
            'last_message' => 'Blokir dibuka oleh Manager/HRD',
        ])->update();

        return $this->response->setJSON(['ok' => true]);
    }
}
