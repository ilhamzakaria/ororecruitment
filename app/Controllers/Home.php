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
        $existingSession = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
        
        $currentSession = (int) ($existingSession['current_session'] ?? 1);

        return view('interview_app', [
            'questions' => $this->getQuestions($currentSession),
            'durationMinutes' => 30,
            'maxViolations' => 1,
            'tabSwitchLimit' => 1,
            'dashboardUrl' => $isHrd ? site_url('dashboard-hrd') : site_url('dashboard-user'),
            'dashboardLabel' => $isHrd ? 'Dashboard HRD' : 'Dashboard Pegawai',
            'monitorEventUrl' => site_url('monitoring/events'),
            'completeSessionUrl' => site_url('tes-interview/complete'),
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
        $sessionId = $json->sessionId ?? '';
        $currentSession = (int)($json->currentSession ?? 1);

        if ($sessionId === '') {
            return $this->response->setJSON(['ok' => false, 'message' => 'Session ID required']);
        }

        $db = \Config\Database::connect();
        
        if ($currentSession < 3) {
            $nextSession = $currentSession + 1;
            $db->table('peserta')->where('session_id', $sessionId)->update([
                'current_session' => $nextSession,
                'status' => 'active', 
                'answers' => null, 
                'current_question' => 0,
            ]);
            return $this->response->setJSON(['ok' => true, 'next' => true, 'session' => $nextSession]);
        } else {
            $db->table('peserta')->where('session_id', $sessionId)->update([
                'status' => 'completed',
                'ended_at' => date('Y-m-d H:i:s'),
            ]);
            return $this->response->setJSON(['ok' => true, 'next' => false]);
        }
    }
}
