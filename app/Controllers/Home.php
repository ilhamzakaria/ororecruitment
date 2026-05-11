<?php

namespace App\Controllers;

use App\Libraries\UserAccountStore;

class Home extends BaseController
{
    private UserAccountStore $accounts;

    public function __construct()
    {
        $this->accounts = new UserAccountStore();
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

        $db = \Config\Database::connect();
        $sessionId = $defaultIdentity['sessionCode'] ?: 'SESI-' . uniqid();

        $this->ensureUserInitialized($authUser, $sessionId, $db);

        // Refresh existing session after initialization
        $existingSession = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
        if (! $existingSession && $authUser) {
            $existingSession = $db->table('peserta')
                ->where('id_user', $authUser['id_user'])
                ->orderBy('updated_at', 'DESC')
                ->get()
                ->getRowArray();
        }

        $currentSession = (int) ($existingSession['current_session'] ?? 1);
        
        // Handle requested session from URL
        $requestedSession = (int) $this->request->getGet('session');
        if ($requestedSession && $requestedSession !== $currentSession) {
            $allowed = false;
            if ($requestedSession === 1) {
                $allowed = true;
            } else {
                // Check if previous session is completed
                $prevSession = $requestedSession - 1;
                $prevStatus = $db->table('status_sesi_peserta')
                    ->where('id_pegawai', $authUser['id_user'] ?? '')
                    ->where('nomor_sesi', $prevSession)
                    ->get()->getRowArray();
                
                if (($prevStatus['status_sesi'] ?? '') === 'selesai') {
                    $allowed = true;
                }
            }

            if ($allowed) {
                $currentSession = $requestedSession;
                // Sync peserta table
                if ($existingSession) {
                    $db->table('peserta')
                        ->where('session_id', $sessionId)
                        ->update(['current_session' => $currentSession]);
                }
            } else {
                return redirect()->to(site_url('tes-interview'));
            }
        }
        
        // Get duration for current session from pengaturan_sesi
        $sessionSetting = $db->table('pengaturan_sesi')->where('id_sesi', $currentSession)->get()->getRowArray();
        $sessionDuration = (int) ($sessionSetting['durasi_menit'] ?? 15);

        // Re-fetch current session status and control after potential initialization
        $sessionStatus = $db->table('status_sesi_peserta')
            ->where('id_pegawai', $authUser['id_user'] ?? '')
            ->where('nomor_sesi', $currentSession)
            ->get()->getRowArray();

        $sessionControl = $db->table('kontrol_sesi_pegawai')
            ->where('id_pegawai', $authUser['id_user'] ?? '')
            ->where('nomor_sesi', $currentSession)
            ->get()->getRowArray();

        $timeLeftSeconds = (int) ($sessionStatus['waktu_sisa'] ?? ($sessionDuration * 60));
        if ($sessionStatus && !empty($sessionStatus['waktu_mulai']) && ($sessionStatus['status_sesi'] ?? '') === 'berjalan') {
            $startTime = strtotime($sessionStatus['waktu_mulai']);
            $now = time();
            $elapsed = $now - $startTime;
            $calcTimeLeft = ($sessionStatus['durasi_menit'] * 60) - $elapsed;
            
            if ($calcTimeLeft < $timeLeftSeconds) {
                $timeLeftSeconds = $calcTimeLeft;
            }
            if ($timeLeftSeconds < 0) $timeLeftSeconds = 0;
        }

        $allQuestions = [];
        $allAnswers = [];
        $sessions = [1, 2, 3];
        
        foreach ($sessions as $s) {
            $allQuestions[$s] = $this->getQuestions($s);
            $allAnswers[$s] = $this->getAnswers($s, $authUser['id_user'] ?? '');
        }

        $allSessionStatuses = [];
        $allSessionControls = [];
        $sessionViolations = [1 => 0, 2 => 0, 3 => 0];

        $allCompleted = true;
        foreach ($sessions as $s) {
            $allSessionStatuses[$s] = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $authUser['id_user'] ?? '')
                ->where('nomor_sesi', $s)
                ->get()->getRowArray();

            $allSessionControls[$s] = $db->table('kontrol_sesi_pegawai')
                ->where('id_pegawai', $authUser['id_user'] ?? '')
                ->where('nomor_sesi', $s)
                ->get()->getRowArray();
            
            if (($allSessionStatuses[$s]['status_sesi'] ?? '') !== 'selesai') {
                $allCompleted = false;
            }

            $vCount = $db->table('log_pelanggaran')
                ->where('id_pegawai', $authUser['id_user'] ?? '')
                ->where('nomor_sesi', $s)
                ->countAllResults();
            $sessionViolations[$s] = $vCount;
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
            'sessionId' => $sessionId,
            'currentSession' => $currentSession,
            'sessionControl' => $sessionControl,
            'sessionStatus' => $sessionStatus,
            'allSessionStatuses' => $allSessionStatuses,
            'allSessionControls' => $allSessionControls,
            'allCompleted' => $allCompleted,
            'sessionViolations' => $sessionViolations,
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
                'sessionCode' => $idUser !== '' ? 'SESI-' . $idUser : '',
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
        $db = \Config\Database::connect();
        $results = $db->table("pertanyaan_sesi_$session")
            ->where('status_pertanyaan', 'Aktif')
            ->orderBy('urutan_pertanyaan', 'ASC')
            ->get()
            ->getResultArray();

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
        $db = \Config\Database::connect();
        $results = $db->table("jawaban_sesi_$session")
            ->where('id_pegawai', $idPegawai)
            ->get()
            ->getResultArray();
        
        $answers = [];
        foreach ($results as $row) {
            if ($session === 1) {
                $pairs = [];
                $mostCsv = trim((string) ($row['jawaban_most'] ?? ''));
                $leastCsv = trim((string) ($row['jawaban_least'] ?? ''));
                $rawJawabanPegawai = trim((string) ($row['jawaban_pegawai'] ?? ''));

                if ($rawJawabanPegawai !== '') {
                    $decoded = json_decode($rawJawabanPegawai, true);
                    if (is_array($decoded) && isset($decoded['pairs']) && is_array($decoded['pairs'])) {
                        /** @var array<string, string> $pairs */
                        $pairs = $decoded['pairs'];
                    }
                }

                if ($pairs === []) {
                    $mostItems = array_filter(array_map('trim', explode(',', strtoupper($mostCsv))));
                    $leastItems = array_filter(array_map('trim', explode(',', strtoupper($leastCsv))));
                    foreach ($mostItems as $item) {
                        $pairs[$item] = 'most';
                    }
                    foreach ($leastItems as $item) {
                        $pairs[$item] = 'least';
                    }
                }

                $mostValues = [];
                $leastValues = [];
                foreach ($pairs as $opt => $pick) {
                    if ($pick === 'most') {
                        $mostValues[] = (string) $opt;
                    } elseif ($pick === 'least') {
                        $leastValues[] = (string) $opt;
                    }
                }

                $answers[$row['id_pertanyaan']] = [
                    'pairs' => $pairs,
                    'most' => $mostValues,
                    'least' => $leastValues,
                ];
            } else {
                $answers[$row['id_pertanyaan']] = $row['jawaban_pegawai'];
            }
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

        $db = \Config\Database::connect();
        $builder = $db->table('peserta');

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

        if ($blocked) {
            return $this->response->setJSON(['blocked' => true]);
        }

        return $this->response->setJSON(['blocked' => false]);
    }

    public function completeSession()
    {
        $json = $this->request->getJSON();
        $sessionId = trim((string) ($json->sessionId ?? ''));
        $currentSession = (int)($json->currentSession ?? 1);
        $idUser = (string) ($this->authUser()['id_user'] ?? '');

        $sessionId = $this->resolveSessionId($sessionId, $idUser);

        if ($sessionId === '' || $idUser === '') {
            return $this->response->setJSON(['ok' => false, 'message' => 'Session ID required']);
        }

        $db = \Config\Database::connect();
        
        // Backend validation removed: allow partial submission
        $questions = $this->getQuestions($currentSession);
        $answers = $this->getAnswers($currentSession, $idUser);

        // Update status for current session
        $db->table('status_sesi_peserta')
            ->where('id_pegawai', $idUser)
            ->where('nomor_sesi', $currentSession)
            ->update([
                'status_sesi' => 'selesai',
                'tanggal_selesai' => date('Y-m-d H:i:s'),
                'waktu_sisa' => 0
            ]);

        // Also update kontrol_sesi_pegawai
        $db->table('kontrol_sesi_pegawai')
            ->where('id_pegawai', $idUser)
            ->where('nomor_sesi', $currentSession)
            ->update([
                'status_sesi' => 'selesai'
            ]);

        if ($currentSession < 3) {
            $nextSession = $currentSession + 1;
            $db->table('peserta')->where('session_id', $sessionId)->update([
                'current_session' => $nextSession,
                'status' => 'active', 
                'answers' => null, 
                'current_question' => 0,
            ]);

            // Initialize next session status
            $sessionSetting = $db->table('pengaturan_sesi')->where('id_sesi', $nextSession)->get()->getRowArray();
            $nextDuration = (int) ($sessionSetting['durasi_menit'] ?? 15);

            $existsStatus = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $idUser)
                ->where('nomor_sesi', $nextSession)
                ->get()->getRowArray();

            if (!$existsStatus) {
                $db->table('status_sesi_peserta')->insert([
                    'id_pegawai' => $idUser,
                    'nomor_sesi' => $nextSession,
                    'durasi_menit' => $nextDuration,
                    'waktu_sisa' => $nextDuration * 60,
                    'status_sesi' => 'belum_mulai'
                ]);
            }

            // Also ensure next session is OPENED in kontrol_sesi_pegawai
            $existsKontrol = $db->table('kontrol_sesi_pegawai')
                ->where('id_pegawai', $idUser)
                ->where('nomor_sesi', $nextSession)
                ->get()->getRowArray();
            
            if ($existsKontrol) {
                $db->table('kontrol_sesi_pegawai')
                    ->where('id_kontrol', $existsKontrol['id_kontrol'])
                    ->update(['status_sesi' => 'belum_dibuka']);
            } else {
                $db->table('kontrol_sesi_pegawai')->insert([
                    'id_pegawai' => $idUser,
                    'nama_pegawai' => $this->authUser()['name'] ?? '',
                    'nomor_sesi' => $nextSession,
                    'status_sesi' => 'belum_dibuka',
                    'tanggal_dibuka' => date('Y-m-d'),
                    'waktu_dibuka' => date('H:i:s'),
                    'dibuka_oleh' => 'System (Waiting for HRD)'
                ]);
            }


            return $this->response->setJSON(['ok' => true, 'next' => true, 'session' => $nextSession]);
        } else {
            $existing = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
            $startedAt = $existing['started_at'] ?? date('Y-m-d H:i:s');
            $endedAt = date('Y-m-d H:i:s');
            
            $start = strtotime($startedAt);
            $end = strtotime($endedAt);
            $durationSeconds = $end - $start;

            $db->table('peserta')->where('session_id', $sessionId)->update([
                'status' => 'completed',
                'ended_at' => $endedAt,
                'durasi_total_detik' => $durationSeconds
            ]);
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
        $idUser = (string) ($this->authUser()['id_user'] ?? '');
        $sessionId = $this->resolveSessionId(trim((string) ($json->sessionId ?? '')), $idUser);

        if ($sessionId === '' || $idUser === '') {
            return $this->response->setJSON(['ok' => false]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'summary' => $this->getSummaryData($idUser, $sessionId)
        ]);
    }

    private function getSummaryData($idUser, $sessionId)
    {
        $db = \Config\Database::connect();
        
        $totalQuestions = 0;
        $totalAnswered = 0;
        
        for ($i = 1; $i <= 3; $i++) {
            $totalQuestions += $db->table("pertanyaan_sesi_$i")->where('status_pertanyaan', 'Aktif')->countAllResults();
            $totalAnswered += $db->table("jawaban_sesi_$i")->where('id_pegawai', $idUser)->countAllResults();
        }

        $peserta = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
        $durationSeconds = (int) ($peserta['durasi_total_detik'] ?? 0);
        
        $violations = $db->table('log_pelanggaran')->where('id_pegawai', $idUser)->countAllResults();

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

    private function resolveSessionId(string $sessionId, string $idUser): string
    {
        if ($idUser === '') {
            return trim($sessionId);
        }

        $sessionId = trim($sessionId);
        $db = \Config\Database::connect();

        if ($sessionId !== '') {
            $exists = $db->table('peserta')
                ->where('session_id', $sessionId)
                ->get()
                ->getRowArray();

            if ($exists) {
                return $sessionId;
            }
        }

        $existingSession = $db->table('peserta')
            ->where('id_user', $idUser)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->getRowArray();

        if ($existingSession && ! empty($existingSession['session_id'])) {
            return (string) $existingSession['session_id'];
        }

        return $sessionId !== '' ? $sessionId : 'SESI-' . $idUser;
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
        $jawabanMost = (string)($json->jawabanMost ?? '');
        $jawabanLeast = (string)($json->jawabanLeast ?? '');
        $jawabanPairs = $json->jawabanPairs ?? null;

        if (!$idPegawai || !$idPertanyaan) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Invalid data']);
        }

        $db = \Config\Database::connect();
        
        // Get correct answer from pertanyaan_sesi_X
        $q = $db->table("pertanyaan_sesi_$session")
            ->where('id_pertanyaan', $idPertanyaan)
            ->get()
            ->getRowArray();
        
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

        if ($session === 1) {
            $pairs = [];
            if (is_object($jawabanPairs)) {
                $jawabanPairs = (array) $jawabanPairs;
            }
            if (is_array($jawabanPairs)) {
                foreach ($jawabanPairs as $opt => $pick) {
                    $opt = strtoupper(trim((string) $opt));
                    $pick = strtolower(trim((string) $pick));
                    if ($opt === '' || ! in_array($pick, ['most', 'least'], true)) {
                        continue;
                    }
                    $pairs[$opt] = $pick;
                }
            }

            $mostValues = [];
            $leastValues = [];
            foreach ($pairs as $opt => $pick) {
                if ($pick === 'most') {
                    $mostValues[] = $opt;
                } elseif ($pick === 'least') {
                    $leastValues[] = $opt;
                }
            }

            if ($pairs === []) {
                $mostValues = array_filter(array_map('trim', explode(',', strtoupper($jawabanMost))));
                $leastValues = array_filter(array_map('trim', explode(',', strtoupper($jawabanLeast))));
                foreach ($mostValues as $opt) {
                    $pairs[$opt] = 'most';
                }
                foreach ($leastValues as $opt) {
                    $pairs[$opt] = 'least';
                }
            }

            $data['jawaban_most'] = implode(',', $mostValues);
            $data['jawaban_least'] = implode(',', $leastValues);
            $data['jawaban_pegawai'] = json_encode(['pairs' => $pairs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            $data['status_jawaban'] = 'Selesai';
            $data['nilai'] = 0;
        }

        $existing = $db->table("jawaban_sesi_$session")
            ->where('id_pegawai', $idPegawai)
            ->where('id_pertanyaan', $idPertanyaan)
            ->get()
            ->getRowArray();

        if ($existing) {
            $db->table("jawaban_sesi_$session")
                ->where('id_jawaban', $existing['id_jawaban'])
                ->update($data);
        } else {
            $db->table("jawaban_sesi_$session")->insert($data);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    /**
     * Ensures all necessary database records exist for the user to participate in the interview.
     */
    private function ensureUserInitialized(?array $authUser, string &$sessionId, $db): void
    {
        $idUser = (string) ($authUser['id_user'] ?? $authUser['id'] ?? '');
        if ($idUser === '') {
            return;
        }

        $idUser = (string) $idUser;
        $existing = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
        
        if (!$existing) {
            $existing = $db->table('peserta')
                ->where('id_user', $idUser)
                ->orderBy('updated_at', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($existing) {
                $sessionId = (string) ($existing['session_id'] ?? $sessionId);
            }
        }

        // 1. Create 'peserta' if totally missing
        if (!$existing) {
            $db->table('peserta')->insert([
                'session_id'       => $sessionId,
                'id_user'          => $idUser,
                'candidate_name'   => $authUser['name'] ?? 'Peserta',
                'position_name'    => $authUser['positionName'] ?? 'Pegawai',
                'hrd_name'         => 'System',
                'session_code'     => $sessionId,
                'status'           => 'draft',
                'current_session'  => 1,
                'is_blocked'       => 0,
                'questions_total'  => 0,
                'violations_count' => 0,
                'tab_switches'     => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 2. Initialize all 3 sessions
        foreach ([1, 2, 3] as $s) {
            // status_sesi_peserta
            $status = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $idUser)
                ->where('nomor_sesi', $s)
                ->get()->getRowArray();
            
            if (!$status) {
                $sSetting = $db->table('pengaturan_sesi')->where('id_sesi', $s)->get()->getRowArray();
                $sDur = (int) ($sSetting['durasi_menit'] ?? 15);
                $db->table('status_sesi_peserta')->insert([
                    'id_pegawai'   => $idUser,
                    'nomor_sesi'   => $s,
                    'durasi_menit' => $sDur,
                    'waktu_sisa'   => $sDur * 60,
                    'status_sesi'  => 'belum_mulai'
                ]);
            }

            // kontrol_sesi_pegawai
            $control = $db->table('kontrol_sesi_pegawai')
                ->where('id_pegawai', $idUser)
                ->where('nomor_sesi', $s)
                ->get()->getRowArray();
            
            if (!$control) {
                $db->table('kontrol_sesi_pegawai')->insert([
                    'id_pegawai'     => $idUser,
                    'nama_pegawai'   => $authUser['name'] ?? 'Peserta',
                    'nomor_sesi'     => $s,
                    'status_sesi'    => ($s === 1) ? 'dibuka' : 'belum_dibuka',
                    'tanggal_dibuka' => date('Y-m-d'),
                    'waktu_dibuka'   => date('H:i:s'),
                    'dibuka_oleh'    => 'System'
                ]);
            }
        }
    }
}
