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

        return view('hrd_dashboard', [
            'summary' => $dashboard['summary'],
            'sessions' => $dashboard['sessions'],
            'recentViolations' => $dashboard['recentViolations'],
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
        $db = \Config\Database::connect();
        return $db->table('jawaban_pegawai')
            ->orderBy('tanggal_menjawab', 'DESC')
            ->get()
            ->getResultArray();
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
