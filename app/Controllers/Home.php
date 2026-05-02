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

        return view('interview_app', [
            'questions' => $this->getQuestions(),
            'durationMinutes' => 30,
            'maxViolations' => 1,
            'tabSwitchLimit' => 1,
            'dashboardUrl' => $isHrd ? site_url('dashboard-hrd') : site_url('dashboard-user'),
            'dashboardLabel' => $isHrd ? 'Dashboard HRD' : 'Dashboard Pegawai',
            'monitorEventUrl' => site_url('monitoring/events'),
            'logoutUrl' => site_url('logout'),
            'authUser' => $authUser,
            'defaultIdentity' => $defaultIdentity,
            'existingSession' => $existingSession,
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
    private function getQuestions(): array
    {
        $db = \Config\Database::connect();
        $results = $db->table('pertanyaan_tes')
            ->where('status_pertanyaan', 'Aktif')
            ->get()
            ->getResultArray();

        $questions = [];
        $i = 1;
        foreach ($results as $q) {
            $questions[] = [
                'id'             => $q['id_pertanyaan'],
                'number'         => $i++,
                'prompt'         => $q['isi_pertanyaan'],
                'tipe'           => $q['tipe_pertanyaan'],
                'promptImageUrl' => $q['gambar_pertanyaan'] ? base_url($q['gambar_pertanyaan']) : null,
                'options'        => [
                    ['value' => 'A', 'label' => 'A', 'text' => $q['pilihan_a']],
                    ['value' => 'B', 'label' => 'B', 'text' => $q['pilihan_b']],
                    ['value' => 'C', 'label' => 'C', 'text' => $q['pilihan_c']],
                    ['value' => 'D', 'label' => 'D', 'text' => $q['pilihan_d']],
                ]
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
}
