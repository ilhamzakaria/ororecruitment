<?php

namespace App\Controllers;

use App\Libraries\UserAccountStore;
use App\Models\ParticipantModel;
use App\Models\SessionStatusModel;
use App\Models\SessionSettingModel;
use App\Models\ViolationLogModel;
use App\Models\SessionQuestionModel;
use App\Models\SessionAnswerModel;

class Home extends BaseController
{
    private UserAccountStore $accounts;
    protected ParticipantModel $participantModel;
    protected SessionStatusModel $sessionStatusModel;
    protected SessionSettingModel $sessionSettingModel;
    protected ViolationLogModel $violationLogModel;
    protected SessionQuestionModel $sessionQuestionModel;
    protected SessionAnswerModel $sessionAnswerModel;

    public function __construct()
    {
        $this->accounts = new UserAccountStore();
        $this->participantModel = new ParticipantModel();
        $this->sessionStatusModel = new SessionStatusModel();
        $this->sessionSettingModel = new SessionSettingModel();
        $this->violationLogModel = new ViolationLogModel();
        $this->sessionQuestionModel = new SessionQuestionModel();
        $this->sessionAnswerModel = new SessionAnswerModel();
    }

    public function index()
    {
        helper('url');

        if (! $this->authAllows(['hrd', 'pegawai', 'manager'])) {
            return redirect()->to(site_url('login'));
        }

        $authUser = $this->authUser();
        $defaultIdentity = $this->buildDefaultIdentity($authUser ?? []);
        $isHrd = (($authUser['role'] ?? '') === 'hrd');

        $sessionId = $defaultIdentity['sessionCode'] ?: 'SESI-' . uniqid();
        $existingSession = $this->participantModel->getBySessionId($sessionId);
        
        $currentSession = (int) ($existingSession['current_session'] ?? 1);
        
        // Get duration for current session from pengaturan_sesi
        $sessionSetting = $this->sessionSettingModel->getBySessionNumber($currentSession);
        $sessionDuration = (int) ($sessionSetting['durasi_menit'] ?? 10);

        // Get or initialize status_sesi_peserta
        $sessionStatus = $this->sessionStatusModel->getStatus($authUser['id_user'] ?? '', $currentSession);

        if (!$sessionStatus && $existingSession) {
            // Initialize if not exists but main session exists
            $this->sessionStatusModel->insert([
                'id_pegawai' => $authUser['id_user'] ?? '',
                'nomor_sesi' => $currentSession,
                'durasi_menit' => $sessionDuration,
                'waktu_sisa' => $sessionDuration * 60,
                'status_sesi' => 'belum_mulai'
            ]);
            $sessionStatus = $this->sessionStatusModel->getStatus($authUser['id_user'] ?? '', $currentSession);
        }

        $timeLeftSeconds = $sessionDuration * 60;
        if ($sessionStatus) {
            if (!$sessionStatus['waktu_mulai'] && $existingSession && $existingSession['started_at']) {
                // If main test has started but this session hasn't, start it now
                $this->sessionStatusModel->update($sessionStatus['id_status'], [
                    'waktu_mulai' => date('Y-m-d H:i:s'),
                    'status_sesi' => 'berjalan'
                ]);
                $sessionStatus['waktu_mulai'] = date('Y-m-d H:i:s');
                $sessionStatus['status_sesi'] = 'berjalan';
            }

            if ($sessionStatus['waktu_mulai']) {
                $startTime = strtotime($sessionStatus['waktu_mulai']);
                $now = time();
                $elapsed = $now - $startTime;
                $timeLeftSeconds = ($sessionStatus['durasi_menit'] * 60) - $elapsed;
                
                // Sync with waktu_sisa if it's smaller
                if (isset($sessionStatus['waktu_sisa']) && $sessionStatus['waktu_sisa'] < $timeLeftSeconds && $sessionStatus['waktu_sisa'] > 0) {
                    $timeLeftSeconds = $sessionStatus['waktu_sisa'];
                }

                if ($timeLeftSeconds < 0) $timeLeftSeconds = 0;
            }
        }

        $allQuestions = [];
        $allAnswers = [];
        $sessions = [1, 2, 3];
        
        foreach ($sessions as $s) {
            $allQuestions[$s] = $this->getQuestions($s);
            $allAnswers[$s] = $this->getAnswers($s, $authUser['id_user'] ?? '');
        }

        // Validate session access strictly
        if ($currentSession >= 2) {
            $s1Total = count($allQuestions[1]);
            $s1Answered = count($allAnswers[1]);
            if ($s1Answered < $s1Total) {
                $currentSession = 1;
            }
        }
        
        if ($currentSession >= 3) {
            $s2Total = count($allQuestions[2]);
            $s2Answered = count($allAnswers[2]);
            if ($s2Answered < $s2Total) {
                $currentSession = min($currentSession, 2);
            }
        }

        return view('interview_app', [
            'allQuestions' => $allQuestions,
            'allAnswers' => $allAnswers,
            'durationMinutes' => $sessionDuration,
            'timeLeftSeconds' => $timeLeftSeconds,
            'maxViolations' => 1,
            'tabSwitchLimit' => 1,
            'dashboardUrl' => $isHrd ? site_url('dashboard-hrd') : site_url('dashboard-user'),
            'dashboardLabel' => $isHrd ? 'Dashboard HRD' : 'Dashboard Pegawai',
            'monitorEventUrl' => site_url('monitoring/events'),
            'completeSessionUrl' => site_url('tes-interview/complete'),
            'saveAnswerUrl' => site_url('tes-interview/save-answer'),
            'summaryUrl' => site_url('tes-interview/summary'),
            'logoutUrl' => site_url('logout'),
            'authUser' => $authUser,
            'defaultIdentity' => $defaultIdentity,
            'existingSession' => $existingSession,
            'currentSession' => $currentSession,
        ]);
    }

    public function dashboardUser()
    {
        helper('url');

        if (! $this->authAllows(['hrd', 'pegawai'])) {
            return redirect()->to(site_url('login'));
        }

        $authUser = $this->authUser() ?? [];
        $role = (string) ($authUser['role'] ?? '');
        if ($role === 'hrd') {
            return redirect()->to(site_url('dashboard-hrd'));
        }

        $idUser = (string) ($authUser['id_user'] ?? '');
        $pegawaiProfile = $this->accounts->findPegawaiProfileByIdUser($idUser);
        $hrdList = $this->accounts->listHrd();

        return view('user_dashboard', [
            'authUser' => $authUser,
            'pegawaiProfile' => $pegawaiProfile,
            'hrdList' => $hrdList,
            'interviewUrl' => site_url('tes-interview'),
            'logoutUrl' => site_url('logout'),
        ]);
    }

    /**
     * @param array<string, mixed> $authUser
     *
     * @return array{candidateName: string, positionName: string, hrdName: string, sessionCode: string}
     */
    private function buildDefaultIdentity(array $authUser): array
    {
        $role = (string) ($authUser['role'] ?? '');
        $idUser = (string) ($authUser['id_user'] ?? '');

        if ($role === 'pegawai') {
            return [
                'candidateName' => (string) ($authUser['name'] ?? $idUser),
                'positionName' => (string) ($authUser['positionName'] ?? ''),
                'hrdName' => (string) ($authUser['hrdName'] ?? ''),
                'sessionCode' => $idUser,
            ];
        }

        return [
            'candidateName' => '',
            'positionName' => '',
            'hrdName' => (string) ($authUser['name'] ?? ''),
            'sessionCode' => $idUser !== '' ? 'SESI-' . $idUser : '',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getQuestions(int $session = 1): array
    {
        $results = $this->sessionQuestionModel->setSession($session)
            ->where('status_pertanyaan', 'Aktif')
            ->orderBy('urutan_pertanyaan', 'ASC')
            ->findAll();

        $questions = [];
        $optionLetters = range('a', 'h');

        foreach ($results as $q) {
            $options = [];
            $maxOptionIndex = -1;

            foreach ($optionLetters as $index => $suffix) {
                $textVal = trim((string) ($q['pilihan_' . $suffix] ?? ''));
                $imgVal = trim((string) ($q['gambar_pilihan_' . $suffix] ?? ''));

                if ($textVal !== '' || $imgVal !== '') {
                    $maxOptionIndex = $index;
                }
            }

            if ($maxOptionIndex < 0) {
                $maxOptionIndex = 3;
            }

            for ($index = 0; $index <= $maxOptionIndex; $index++) {
                $suffix = $optionLetters[$index];
                $label = strtoupper($suffix);
                $tipe = $q['tipe_pilihan_' . $suffix] ?? 'text';
                $textVal = trim((string) ($q['pilihan_' . $suffix] ?? ''));
                $imgVal = trim((string) ($q['gambar_pilihan_' . $suffix] ?? ''));
                $text = $tipe === 'text' ? $textVal : null;

                if (! empty($q['gambar_pertanyaan']) && $text === $label && $imgVal === '') {
                    $text = null;
                }

                $options[] = [
                    'value'    => $label,
                    'label'    => $label,
                    'text'     => $text,
                    'imageUrl' => $tipe === 'gambar' && $imgVal !== '' ? base_url($imgVal) : null,
                ];
            }

            $questions[] = [
                'id'             => (string) $q['id_pertanyaan'],
                'number'         => $q['urutan_pertanyaan'],
                'prompt'         => $q['isi_pertanyaan'],
                'promptImageUrl' => $q['gambar_pertanyaan'] ? base_url($q['gambar_pertanyaan']) : null,
                'options'        => $options,
            ];
        }

        return $questions;
    }

    private function getAnswers(int $session, string $idPegawai): array
    {
        $results = $this->sessionAnswerModel->setSession($session)
            ->where('id_pegawai', $idPegawai)
            ->findAll();
        
        $answers = [];
        foreach ($results as $row) {
            $answers[$row['id_pertanyaan']] = $row['jawaban_pegawai'];
        }
        return $answers;
    }

    public function checkStatus()
    {
        $json = $this->request->getJSON();
        $name = trim($json->candidateName ?? '');
        $sessionCode = trim($json->sessionCode ?? '');

        if ($name === '' && $sessionCode === '') {
            return $this->response->setJSON(['blocked' => false]);
        }

        $builder = $this->participantModel->builder();

        $builder->groupStart();
        if ($name !== '') {
            $builder->orWhere('candidate_name', $name);
        }
        if ($sessionCode !== '') {
            $builder->orWhere('session_code', $sessionCode);
        }
        $builder->groupEnd();

        $builder->where('is_blocked', 1);

        $blocked = $builder->get()->getRowArray();

        return $this->response->setJSON(['blocked' => (bool)$blocked]);
    }

    public function completeSession()
    {
        $json = $this->request->getJSON();
        $sessionId = $json->sessionId ?? '';
        $currentSession = (int)($json->currentSession ?? 1);

        if ($sessionId === '') {
            return $this->response->setJSON(['ok' => false, 'message' => 'Session ID required']);
        }

        $idUser = $this->authUser()['id_user'] ?? '';
        
        // Backend validation: Ensure all questions are answered
        $questions = $this->getQuestions($currentSession);
        $answers = $this->getAnswers($currentSession, $idUser);
        
        if (count($answers) < count($questions)) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Selesaikan semua soal pada sesi ini terlebih dahulu.']);
        }

        // Update status for current session
        $this->sessionStatusModel->where('id_pegawai', $idUser)
            ->where('nomor_sesi', $currentSession)
            ->set([
                'status_sesi' => 'selesai',
                'tanggal_selesai' => date('Y-m-d H:i:s'),
                'waktu_sisa' => 0
            ])->update();

        if ($currentSession < 3) {
            $nextSession = $currentSession + 1;
            $this->participantModel->where('session_id', $sessionId)->set([
                'current_session' => $nextSession,
                'status' => 'active', 
                'answers' => null, 
                'current_question' => 0,
            ])->update();

            // Initialize next session status
            $sessionSetting = $this->sessionSettingModel->getBySessionNumber($nextSession);
            $nextDuration = (int) ($sessionSetting['durasi_menit'] ?? 10);

            $this->sessionStatusModel->insert([
                'id_pegawai' => $idUser,
                'nomor_sesi' => $nextSession,
                'durasi_menit' => $nextDuration,
                'waktu_sisa' => $nextDuration * 60,
                'status_sesi' => 'belum_mulai'
            ]);

            return $this->response->setJSON(['ok' => true, 'next' => true, 'session' => $nextSession]);
        } else {
            $existing = $this->participantModel->getBySessionId($sessionId);
            $startedAt = $existing['started_at'] ?? date('Y-m-d H:i:s');
            $endedAt = date('Y-m-d H:i:s');
            
            $start = strtotime($startedAt);
            $end = strtotime($endedAt);
            $durationSeconds = $end - $start;

            $this->participantModel->where('session_id', $sessionId)->set([
                'status' => 'completed',
                'ended_at' => $endedAt,
                'durasi_total_detik' => $durationSeconds
            ])->update();
            
            return $this->response->setJSON([
                'ok' => true, 
                'next' => false,
                'summary' => $this->getSummaryData($idUser, $sessionId)
            ]);
        }
    }

    public function getSummary()
    {
        $json = $this->request->getJSON();
        $sessionId = $json->sessionId ?? '';
        $idUser = $this->authUser()['id_user'] ?? '';

        if (!$sessionId) {
            return $this->response->setJSON(['ok' => false]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'summary' => $this->getSummaryData($idUser, $sessionId)
        ]);
    }

    private function getSummaryData($idUser, $sessionId)
    {
        $totalQuestions = 0;
        $totalAnswered = 0;
        
        for ($i = 1; $i <= 3; $i++) {
            $totalQuestions += $this->sessionQuestionModel->setSession($i)->where('status_pertanyaan', 'Aktif')->countAllResults();
            $totalAnswered += $this->sessionAnswerModel->setSession($i)->where('id_pegawai', $idUser)->countAllResults();
        }

        $peserta = $this->participantModel->getBySessionId($sessionId);
        $durationSeconds = (int) ($peserta['durasi_total_detik'] ?? 0);
        
        $violations = $this->violationLogModel->countByPegawai($idUser);

        // Format duration
        $h = floor($durationSeconds / 3600);
        $m = floor(($durationSeconds % 3600) / 60);
        $s = $durationSeconds % 60;

        $durationText = "";
        if ($h > 0) {
            $durationText .= $h . "j ";
        }
        $durationText .= $m . "m " . $s . "s";

        return [
            'totalQuestions' => $totalQuestions,
            'totalAnswered' => $totalAnswered,
            'durationSeconds' => $durationSeconds,
            'durationText' => trim($durationText),
            'violations' => $violations
        ];
    }

    public function saveAnswer()
    {
        $json = $this->request->getJSON();
        $session = (int)($json->session ?? 1);
        $idPegawai = (string)($json->idPegawai ?? '');
        $namaPegawai = (string)($json->namaPegawai ?? '');
        $idPertanyaan = (int)($json->idPertanyaan ?? 0);
        $nomorPertanyaan = (int)($json->nomorPertanyaan ?? 0);
        $jawabanPegawai = (string)($json->jawabanPegawai ?? '');

        if (!$idPegawai || !$idPertanyaan) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Invalid data']);
        }

        $q = $this->sessionQuestionModel->setSession($session)->find($idPertanyaan);
        
        if (!$q) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Question not found']);
        }

        $jawabanBenar = $q['jawaban_benar'] ?? '';
        $statusJawaban = (strtoupper($jawabanPegawai) === strtoupper($jawabanBenar)) ? 'Benar' : 'Salah';
        $nilai = ($statusJawaban === 'Benar') ? 1 : 0;

        $data = [
            'id_pegawai' => $idPegawai,
            'nama_pegawai' => $namaPegawai,
            'id_pertanyaan' => $idPertanyaan,
            'nomor_pertanyaan' => $nomorPertanyaan,
            'jawaban_pegawai' => strtoupper($jawabanPegawai),
            'jawaban_benar' => strtoupper($jawabanBenar),
            'status_jawaban' => $statusJawaban,
            'nilai' => $nilai,
            'tanggal_menjawab' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->sessionAnswerModel->setSession($session)
            ->where('id_pegawai', $idPegawai)
            ->where('id_pertanyaan', $idPertanyaan)
            ->first();

        if ($existing) {
            $this->sessionAnswerModel->setSession($session)->update($existing['id_jawaban'], $data);
        } else {
            $this->sessionAnswerModel->setSession($session)->insert($data);
        }

        return $this->response->setJSON(['ok' => true]);
    }
}
